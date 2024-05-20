(function (_, $) {
  var createMoment = function (input) {
    // Unix timestamp
    if (isFinite(input)) {
      return moment.unix(input);
    }
    // ISO 8601
    else {
      return moment(input, moment.ISO_8601);
    }
  };
  let callHandler = (fn, args, thisArg) => {
    thisArg = thisArg || this;
    if (typeof fn === 'function') {
      return fn.apply(thisArg, args);
    } else if (window[fn] && typeof window[fn] === 'function') {
      return window[fn].apply(thisArg, args);
    } else {
      console.error("".concat(fn, " is not a valid callback"));
    }
  };
  var methods = {
    init: function (params) {
      var $dateRangePickers = $(this);
      if (typeof moment === 'undefined') {
        $.loadCss(['js/lib/daterangepicker/daterangepicker.css']);
        $.getScript('js/lib/daterangepicker/moment.min.js', function () {
          $.getScript('js/lib/daterangepicker/daterangepicker.js', function () {
            return $dateRangePickers.ceDateRangePicker();
          });
        });
        return false;
      }
      if (!$dateRangePickers.length) {
        return;
      }
      moment.updateLocale(_.tr("default_lang"), {
        monthsShort: [_.tr("month_name_abr_1"), _.tr("month_name_abr_2"), _.tr("month_name_abr_3"), _.tr("month_name_abr_4"), _.tr("month_name_abr_5"), _.tr("month_name_abr_6"), _.tr("month_name_abr_7"), _.tr("month_name_abr_8"), _.tr("month_name_abr_9"), _.tr("month_name_abr_10"), _.tr("month_name_abr_11"), _.tr("month_name_abr_12")]
      });
      moment.locale(_.tr("default_lang"));
      var default_params = {
        ranges: {
          [_.tr('today')]: [moment().startOf('day'), moment().endOf('day')],
          [_.tr('yesterday')]: [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
          [_.tr('this_month')]: [moment().startOf('month'), moment().endOf('month')],
          [_.tr('last_month')]: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
          [_.tr('this_year')]: [moment().startOf('year').startOf('day'), moment().endOf('year').endOf('day')],
          [_.tr('last_year')]: [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
        },
        locale: {
          applyLabel: _.tr("apply"),
          cancelLabel: _.tr("cancel"),
          clearLabel: _.tr("clear"),
          fromLabel: _.tr("from"),
          toLabel: _.tr("to"),
          customRangeLabel: _.tr("custom_range"),
          monthNames: [_.tr("month_name_abr_1"), _.tr("month_name_abr_2"), _.tr("month_name_abr_3"), _.tr("month_name_abr_4"), _.tr("month_name_abr_5"), _.tr("month_name_abr_6"), _.tr("month_name_abr_7"), _.tr("month_name_abr_8"), _.tr("month_name_abr_9"), _.tr("month_name_abr_10"), _.tr("month_name_abr_11"), _.tr("month_name_abr_12")],
          daysOfWeek: [_.tr("weekday_abr_0"), _.tr("weekday_abr_1"), _.tr("weekday_abr_2"), _.tr("weekday_abr_3"), _.tr("weekday_abr_4"), _.tr("weekday_abr_5"), _.tr("weekday_abr_6")]
        },
        showDropdowns: true,
        autoApply: true
      };

      // but, if we had .admin-content and RTL enabled, place picker in this wrapper
      if ($('.admin-content').length && Tygh.language_direction === 'rtl') {
        default_params.parentEl = '.admin-content';
      }
      if (_.time_from) {
        default_params.startDate = createMoment(_.time_from);
      }
      if (_.time_to) {
        default_params.endDate = createMoment(_.time_to);
      }
      var periods = {
        [_.tr('today')]: 'D',
        [_.tr('yesterday')]: 'LD',
        [_.tr('this_month')]: 'M',
        [_.tr('last_month')]: 'LM',
        [_.tr('this_year')]: 'Y',
        [_.tr('last_year')]: 'LY'
      };
      return $dateRangePickers.each(function () {
        var $self = $(this);
        if ($self.data('daterangepicker')) {
          return;
        }
        var element_params = $.extend(true, {}, default_params);
        if (!$self.data('caShowRanges')) {
          delete element_params.ranges;
        }
        if ($self.data('caTimeFrom')) {
          element_params.startDate = createMoment($self.data('caTimeFrom'));
        }
        if ($self.data('caTimeTo')) {
          element_params.endDate = createMoment($self.data('caTimeTo'));
        }
        if ($self.data('caDateFormat')) {
          element_params.format = $self.data('caDateFormat');
          element_params.locale.format = $self.data('caDateFormat');
        }
        if ($self.data('caMinDate')) {
          element_params.minDate = createMoment($self.data('caMinDate'));
        }
        if ($self.data('caMaxDate')) {
          element_params.maxDate = createMoment($self.data('caMaxDate'));
        }
        if ($self.data('caMaxSpan')) {
          element_params.maxSpan = $self.data('caMaxSpan');
        }
        if ($self.data('caAlwaysShowCalendars')) {
          element_params.alwaysShowCalendars = !!$self.data('caAlwaysShowCalendars');
        }
        if ($self.data('caUnavailableDates')) {
          element_params.isInvalidDate = function (day) {
            return $(this)[0].element.data('caUnavailableDates').some(unavailableDate => unavailableDate === day.format('YYYY-MM-DD'));
          };
          element_params.isCustomDate = function (day) {
            return $(this)[0].element.data('caUnavailableDates').some(unavailableDate => unavailableDate === day.format('YYYY-MM-DD')) ? 'ty-date-range__unavailable-day' : '';
          };
        }
        if ($self.data('caCustomDateHandler')) {
          let originalCustomDateHandler = element_params.isCustomDate || (() => {
            return '';
          });
          element_params.isCustomDate = day => {
            let picker = $self.data('daterangepicker');
            return (callHandler($self.data('caCustomDateHandler'), [day, $self], picker) + ' ' + callHandler(originalCustomDateHandler, [day, $self], picker)).trim();
          };
        }
        if ($self.data('caCalendarPrependMessage')) {
          $self.on('show.daterangepicker', (showEvent, picker) => {
            var $calendarContainer = picker.container;
            if ($calendarContainer.data('isCalendarMessagePrepended')) {
              return;
            }
            $calendarContainer.prepend($self.data('caCalendarPrependMessage'));
            $calendarContainer.data('isCalendarMessagePrepended', true);
          });
        }
        if ($self.data('caLinkedCalendars') !== 'undefined') {
          element_params.linkedCalendars = !!$self.data('caLinkedCalendars');
        }
        var settings = $.extend(true, {}, element_params, params);
        $self.daterangepicker(settings, function (start, end, label) {
          var query_params;
          start = moment(start).local().startOf('day');
          end = moment(end).local().endOf('day');
          var selected_from = parseInt(start.valueOf() / 1000, 10);
          var selected_to = parseInt(end.valueOf() / 1000, 10);
          if ($self.data('caDisableInvalidDateInRange') && methods.isInvalidDateInRange($self, start, end)) {
            methods.setOldDates($self, start, end);
            $self.data('caHadInvalidDateInRange', true);
            return;
          }
          if ($self.data('caUsePredefinedPeriods') && periods[label] != undefined) {
            query_params = 'time_period=' + periods[label];
          } else {
            query_params = 'time_from=' + selected_from + '&time_to=' + selected_to;
          }
          $('.cm-date-range__selected-date', $self).html(start.format($self.data('caDisplayedFormat')) + ' — ' + end.format($self.data('caDisplayedFormat')));
          if ($self.data('ca-target-url') && $self.data('ca-target-id')) {
            $.ceAjax('request', $.attachToUrl($self.data('ca-target-url'), query_params), {
              result_ids: $self.data('ca-target-id'),
              caching: false,
              force_exec: true
            });
          }
          methods.updateInputs($self, start, end);
          methods.updateTimeCount($self, start, end);
          if ($self.data('caEvent')) {
            $.ceEvent('trigger', $self.data('caEvent'), [$self, selected_from, selected_to, start, end]);
          }
        });
        if ($self.data('caCalendarRenderHandler')) {
          $self.on('renderCalendar.daterangepicker', (renderEvent, picker) => {
            callHandler($self.data('caCalendarRenderHandler'), [picker, $self]);
          });
        }
      });
    },
    isInvalidDateInRange: function ($el, start, end) {
      return !!$el.data('caUnavailableDates') && $el.data('caUnavailableDates').some(unavailableDate => {
        return createMoment(unavailableDate).isBetween(start, end);
      });
    },
    setOldDates: function ($el, start, end) {
      var daterangepicker = $el.data('daterangepicker');
      $el.data('caEndDateFailed', end.format('YYYY-MM-DD'));
      daterangepicker.setStartDate(daterangepicker.oldStartDate);
      daterangepicker.setEndDate(daterangepicker.oldEndDate);
    },
    updateInputs: function ($el, start, end) {
      var $dateRange = $el.closest('.cm-date-range');
      var selected_from_formated = start.format($el.data('caDateFormat'));
      var selected_to_formated = end.format($el.data('caDateFormat'));
      $('[data-ca-date-range-picker="date-in"]', $dateRange).val(selected_from_formated);
      $('[data-ca-date-range-picker="date-out"]', $dateRange).val(selected_to_formated);
    },
    updateTimeCount: function ($el, start, end) {
      var timeCount = moment.duration(end.startOf('day').diff(start));
      $el.data('caTimeCount', timeCount.asSeconds());
    }
  };
  $.fn.ceDateRangePicker = function (method) {
    if (methods[method]) {
      return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    } else if (typeof method === 'object' || !method) {
      return methods.init.apply(this, arguments);
    } else {
      $.error('ty.tooltip: method ' + method + ' does not exist');
    }
  };
  $.ceEvent('on', 'ce.commoninit', function (context) {
    $dateRange = $('.cm-date-range', context);
    if (!$dateRange.length) {
      return;
    }
    $dateRange.ceDateRangePicker();
  });
})(Tygh, Tygh.$);