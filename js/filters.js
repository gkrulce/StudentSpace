angular.module('spaceFilters', []).

filter('firstLetter', function() {
  return function(input) {
    return input[0].toUpperCase();
  };
});
