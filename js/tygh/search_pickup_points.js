(function (_, $) {
  // Define pseudo-selectors in jquery
  $.expr[':'].contains_case_insensitive = $.expr.createPseudo(function (arg) {
    return elem => searchPickupPoints($(elem), arg);
  });
  $.expr[':'].not_contains_case_insensitive = $.expr.createPseudo(function (arg) {
    return elem => !searchPickupPoints($(elem), arg);
  });
  searchPickupPoints = (element, text) => {
    let isFound = true;
    let elementText = element.text().replace(/[&\/\\#,+()$~%.'":*?<>{}\s]/g, '');
    let inputText = text.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '').split(' ');
    for (let i = 0; i < inputText.length; i++) {
      if (inputText[i].length > 0) {
        isFound = elementText.toUpperCase().indexOf(inputText[i].toUpperCase()) >= 0;
        if (!isFound) {
          break;
        }
      }
    }
    return isFound;
  };
})(Tygh, Tygh.$);