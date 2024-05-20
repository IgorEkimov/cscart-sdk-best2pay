(function (_, $) {
  $('[data-og-accordion="item-head"]').on('click', function (e) {
    e.preventDefault();
    const $this = $(this);
    const accordionItem = $this.closest('[data-og-accordion="item"]');
    const accordionItemBody = accordionItem.find('[data-og-accordion="item-body"]').first();
    $('[data-og-accordion="item-body"]').not(accordionItemBody).slideUp(400);
    $('[data-og-accordion="item"]').not(accordionItem).removeClass('onboarding_accordion__item--active');
    accordionItem.toggleClass('onboarding_accordion__item--active');
    accordionItemBody.slideToggle(400);
    if (accordionItem.hasClass('onboarding_accordion__item--active')) {
      setTimeout(function () {
        $('.onboarding_accordion__item--active').get(0).scrollIntoView({
          behavior: 'smooth'
        });
      }, 450);
    }
  });
  $('.og-step-status').on('change', function () {
    const $this = $(this);
    const isCompleted = $this.prop('checked');
    const stepId = $.ceOnboardingGuideGetStepId($this);
    if (isCompleted) {
      $.ceOnboardingGuideStep('complete', stepId);
    } else {
      $.ceOnboardingGuideStep('close', stepId);
    }
  });
  $('.og-step-complete').on('click', function () {
    const $this = $(this);
    const stepId = $.ceOnboardingGuideGetStepId($this);
    $.ceOnboardingGuideStep('complete', stepId);
  });
  $('.og-action').on('click', function () {
    const $this = $(this);
    const stepId = $.ceOnboardingGuideGetStepId($this);
    const action = $this.data('ogAction');
    if (!action) {
      return;
    }
    $.ceOnboardingGuideAction('complete', stepId, action);
  });
  $('#og-dismiss-button').on('click', function () {
    $.ceOnboardingGuideDismiss();
  });
  $.ceOnboardingGuideGetStepId = function (el) {
    return el.closest('.og-step').data('stepId');
  };
  $.ceOnboardingGuideStep = function (command, stepId) {
    request('step_' + command, {
      step_id: stepId,
      result_ids: 'og-progress-container'
    });
  };
  $.ceOnboardingGuideAction = function (command, stepId, action) {
    request('action_' + command, {
      step_id: stepId,
      action: action
    });
  };
  $.ceOnboardingGuideDismiss = function () {
    request('onboarding_dismiss');
  };
  $.ceOnboardingGuideRestart = function () {
    request('onboarding_restart');
  };
  function request(command) {
    let params = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    $.ceAjax('request', fn_url('onboarding_guide.' + command), {
      method: 'post',
      data: params,
      callback: function (response) {
        if (response.is_success) {
          $.ceEvent('trigger', 'ce.onboarding_guide.after_' + command, [params, response]);
        }
        if (response.onboarding_guide_progress) {
          $.ceEvent('trigger', 'ce.onboarding_guide.progress_change', [response.onboarding_guide_progress]);
        }
      }
    });
  }
  function getStepElement(stepId) {
    let childSelector = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    const step = $('.og-step[data-step-id=' + stepId + ']');
    if (childSelector) {
      return step.find(childSelector);
    }
    return step;
  }

  // EVENT HANDLERS
  $.ceEvent('on', 'ce.onboarding_guide.after_step_complete', function (params) {
    getStepElement(params.step_id, '.og-step-status').prop('checked', true);
    const tooltip = $('[data-og-status-tooltip-step="' + params.step_id + '"]');
    const hasTip = tooltip.attr("title") === undefined;
    try {
      if (hasTip) {
        const tip = tooltip.tooltip().getTip();
        tip.html(tip.html().replace(_.tr('onboarding_guide.step_complete'), _.tr('onboarding_guide.step_close')));
      } else {
        tooltip.attr('title', _.tr('onboarding_guide.step_close'));
        tooltip.ceTooltip();
      }
    } catch (error) {
      console.error(error);
    }
  });
  $.ceEvent('on', 'ce.onboarding_guide.after_step_close', function (params) {
    getStepElement(params.step_id).hide(400);
  });
  $.ceEvent('on', 'ce.onboarding_guide.after_action_complete', function (params, response) {
    const completedActions = response.completed_actions.length;
    const totalActions = getStepElement(params.step_id, '.og-action').length;
    if (completedActions >= totalActions) {
      $.ceOnboardingGuideStep('complete', params.step_id);
    }
  });
  $.ceEvent('on', 'ce.onboarding_guide.after_onboarding_dismiss', function () {
    $("#onboarding-guide").hide('slow');
  });
})(Tygh, Tygh.$);