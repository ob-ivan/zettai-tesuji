
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
     *      ajaxPath    : <string>,
     *          Путь, на который отправлять аякс-запрос.
     *
     *      answers     : <jQuery>,
     *          Контейнеры, в которые поместить ответы.
     *          Должны внутри себя иметь элементы:
     *              span.letter     Куда поместить букву ответа.
     *              span.discard    Куда поместить картинку фишки сброса.
     *              div.comment     Куда поместить текст ответа.
     *                                  
     *      buttons     : <jQuery>,
     *          Кнопки ответов, на которые навесить обработчик.
     *          Должны обладать атрибутом name, значение которого
     *          и считается выбором пользователя.
     *
     *      csrf        : <string>,
     *          Без какого-либо особого смысла.
     *
     *      hide        : <jQuery>,
     *          Элемент, который надо скрыть при показе ответов.
     *
     *      show        : <jQuery>,
     *          Элемент, который надо показать при показе ответов.
     *
     *      next        : <jQuery>,
     *          Элемент ссылки на следующую задачу, который надо
     *          наполнить и показать после показа ответов.
     *  } options
    **/
    __construct : function (options) {
        this.ajaxPath = options.ajaxPath;
        this.answers  = options.answers;
        
        var handle = this._handle;
        options.buttons.on('click', function (event) {
            handle(this, event);
        });
        
        this.csrf = options.csrf;
        this.hide = options.hide;
        this.show = options.show;
        this.next = options.next;
    },
    _handle : function (element, event) {
        var show = this._show;
        $.post(
            this.ajaxPath,
            {
                csrf : this.csrf
            },
            /**
             *  Получает ответ от сервера и передаёт в обработчик.
             *
             *  @param  {
             *      answers         : ...,
             *      best_answer     : <abc>,
             *      exercise_next   : <string>, // Может отсутствовать.
             *  }
            **/
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
                    $(element).attr('name'),
                    data.answers,
                    data.best_answer,
                    data.exercise_next
                );
            },
            'json'
        )
    },
    /**
     * Показывает ответы и навигацию.
     *
     *  @param  <abc>       user_answer
     *  @param  { <abc> : {
     *      discard : <html>,
     *      comment : <html>
     *  } }                 answers
     *  @param  <abc>       best_answer
     *  @param  <string>    exercise_next   Optional.
    **/
    _show : function (user_answer, answers, best_answer, exercise_next) {
    
        // Заполнить ответы.
        var letters = [best_answer];
        for (var letter in answers) {
            if (letter !== best_answer) {
                letters.push(letter);
            }
        }
        for (var i = 0; i < letters.length; ++i) {
            var letter = letters[i];
            var container = $(this.answers[i]);
            container.find('span.letter').text(letter.toUpperCase());
            container.find('span.discard').html(answers[letter].discard);
            container.find('div.comment').html(answers[letter].comment);
            if (user_answer === letter) {
                if (letter === best_answer) {
                    container.addClass('win');
                } else {
                    container.addClass('fail');
                }
            }
        }
        
        // Скрыть вопросы, показать ответы.
        this.hide.hide();
        this.show.show();
        
        // Показать ссылку на следующую задачу.
        if (exercise_next) {
            this.next.attr('href', exercise_next).show();
        }
    }
});

