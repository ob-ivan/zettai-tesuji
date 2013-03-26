
// require: jquery.js, class.js
/**
 * Поведение страницы /exercise/{exercise_id}.
 *
 * Когда пользователь выбирает один из ответов:
 *  - С сервера запрашиваются авторские ответы.
 *  - Показываются все ответы, выбранный пользователем ответ подсвечен.
 *  - Показывается навигация к следующей задаче.
**/
var ExercisePage = Class({
    /**
     * Конструирует объект поведения, навешивает обработчики.
     *
     *  @param  {
     *      ajaxPath    : <string>,     Путь, на который отправлять аякс-запрос.
     *      buttons     : <jQuery>,     Кнопки ответов, на которые навесить обработчик.
     *                                  Должны обладать атрибутом name, значение которого
     *                                  и считается выбором пользователя.
     *      csrf        : <string>      Без какого-либо особого смысла.
     *  } options
    **/
    __construct : function (options) {
        this.ajaxPath = options.ajaxPath;
        
        var handle = this._handle;
        options.buttons.on('click', function (event) {
            handle(this, event);
        });
        
        this.csrf = options.csrf;
    },
    _handle : function (element, event) {
        var show = this._show;
        $.post(
            this.ajaxPath,
            {
                csrf : this.csrf
            },
            function (data, textStatus, jqXHR) {
                // Если контроллер не принял входные данные, показать на экране, чем он недоволен.
                if (typeof data.errors !== 'undefined') {
                    var sadText = [];
                    for (var i in data.errors) {
                        switch (data.errors[i]) {
                            case 'EXERCISE:DOES_NOT_EXIST':
                                sadText.push('Этой задачи больше не существует. Ничего не поделаешь.');
                                break;
                            
                            case 'CSRF':
                                sadText.push('Сессия устарела. Обновите, пожалуйста, страницу и попробуйте ещё раз.');
                                break;
                        }
                    }
                    window.alert(sadText.join('\n'));
                    return;
                }
                // Отобразить ответы и навигацию.
                show(
                    data.answer,
                    data.best_answer,
                    data.exercise_next_id
                );
            },
            'json'
        )
    },
    _show : function (answer, best_answer, exercise_next_id) {
        window.alert([
            'answer = ' + answer,
            'best_answer = ' + best_answer,
            'exercise_next_id = ' + exercise_next_id
        ].join('\n')); // debug
    }
});

