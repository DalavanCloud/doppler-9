// Copyright (c) 2011 Cristian Adamo.
// Use of this source code is governed by a Apache License (v2.0) that can be
// found in the LICENSE file.


/**
 * JAGGER INITIALIZATION
 */
(function() {

  if (window.CX) {
    return;
  }

  CX = {
    /**
     * Basic object installation
     *
     * NOTE: if you new object has a member called construct, it will be called
     * on you create a new instance of it.
     */
    provide: function(name, structure) {
      var jagger = window.CX;
      jagger[name] = {};

      CX.log('Creating class ' + name);
      var Class = (function(name, structure){
        return function() {
          return (structure.construct || CX.limbo).apply(this, arguments);
        };
      })(name, structure);

      CX.log('Applying members... ');
      var elem = Class.prototype = {};
      for (member_name in structure) {
        elem[member_name] = structure[member_name];
      }

      if (!Class) {
        throw new Error ('Failed trying to install object ' + name);
      }
      jagger[name] = Class;
      CX.log('Class ' + name + ' was successfully created');
      return Class;
    },

    id: function(obj) {
      return obj;
    },

    /**
     * Basic binding function.
     */
    bind: function(context, func) {
      return function() {
        return func.apply(context || window, arguments);
      };
    },

    /**
     * Put stuff on the limbo ha ha ha.
     */
    limbo: function() {
      // do nothing
    },

    load: function(func, arg) {
      try {
        func(arg);
      } catch (x) {
        //
      }
    },

    /**
     * Basic Debug instrumentations
     *
     * It's HIGLY recommended just pass the object itself, and not a internal
     * variable, since you could get the entire values of the object, on the
     * console of your browser (like inspector on Google Chome or Firebug on
     * Firefox).
     *
     * @param wild          Object  which we'll get the information.
     * @param string|null   The mechanism used to show the debug information:
     *                      'console' or 'popup'. By default console is set.
     */
    log: function(debug_object, mechanism) {
      var _object = '##CXLogData## ' + debug_object;
      var _mechanism = mechanism || 'console';

      switch (_mechanism) {
        case 'popup':
          alert(_object);
          break;
        case 'console':
        default:
          console.log(_object);
          break;
      }
    }
  };
})();
