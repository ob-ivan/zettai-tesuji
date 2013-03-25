
/**
 * Поведение страницы /exercise/{exercise_id}.
 *
 * Когда пользователь выбирает один из ответов,
 * у контроллера /exercise/answer/{exercise_id}
 * 
**/
var ExercisePage = (function() {
    
    var methods = {
        // <method> : function <method> (<arguments>) { <body> },
    };
    
    // constructor
    return function ExercisePage(exercise_id) {
        var private = {
            exercise_id : exercise_id
        };
        
        for (var method in methods) {
            this[method] = private[method] = methods[method].bind(private);
        }
    };
})();

