
/**
 * Конструирует класс из описания:
 *  - набор методов, 
 *      - если название начинается с подчёркивания,
 *        то метод будет доступен только локально,
 *        иначе же он будет публичным.
 *  - значения локальных переменных по умолчанию.
 *
 * На выходе отдаёт конструктор, который:
 *  - наполняющий новосозданный объект методами,
 *  - наполняет локальные переменные значениями по умолчанию,
 *  - если определён метод __construct, то вызывает его с переданными аргументами.
 *
 * Все методы привязываются к объекту локальных свойств.
 *
 *  @param  { <methodName> : <function> }   methods
 *  @param  { <varName>    : <varValue> }   defaults
 *  @return function Конструктор
**/
var Class = function Class(methods, defaults) {
    return function () {
        var local = Object.create(defaults || Object.prototype);
        
        for (var method in methods) {
            local[method] = methods[method].bind(local);
            if (! method.match(/^_/)) {
                this[method] = local[method];
            }
        }
        
        if (typeof local.__construct !== 'undefined') {
            local.__construct.apply(local, arguments);
        }
    };
};
