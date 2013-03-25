
// require: jquery.js, class.js
/**
 * Поведение страницы /exercise/{exercise_id}.
 *
 * Когда пользователь выбирает один из ответов:
 *  - У контроллера /exercise/answer/{exercise_id} запрашиваются авторские ответы.
 *  - Показываются все ответы, выбранный пользователем ответ подсвечен.
 *  - Показывается навигация к следующей задаче.
**/
var ExercisePage = Class({
    /**
     * Конструирует объект поведения, навешивает обработчики.
     *
     *  @param  {
     *      exercise_id : <integer>,
     *      buttons     : <jQuery>
     *  } options
    **/
    __construct : function (options) {
        this.exercise_id = parseInt(options.exercise_id, 10);
        
        var handle = this._handle;
        options.buttons.on('click', function (event) {
            handle(this, event);
        });
    },
    _handle : function (element, event) {
        alert(1); // debug
    }
});

