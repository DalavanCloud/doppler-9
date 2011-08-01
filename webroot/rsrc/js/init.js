// Copyright (c) 2011 Cristian Adamo. All rights reserved.
// Use of this source code is governed by a Apache License (v2.0) that can be
// found in the LICENSE file.


/**
 * JAGGER INITIALIZATION
 */
(function() {

  if (window.JG) {
    return;
  }

  /**
   * TOOLS
   */
  function id(obj) {
    return obj;
  };

  window.JG = {
    /**
     * Basic object installation
     */
    Provide: function(name, structure) {
      var jagger = window.JG[name];

      var Class = (function(name, structure){
        var ret = function() {
          (structure.construct || JG.Null).apply(this, arguments);
        };
        return ret;
      })(name,structure);

      for (var value in structure) {
        Class[value] = structure[value];
      }

      if (!Class) {
        throw new Error ('Failed trying to install object ' + name);
      }
      jagger[name] = Class;
      return jagger[name];
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
      _object : 'JGLogData: ' + debug_object;
      _mechanism : mechanism || 'console';

      switch (_mechanism) {
        case 'popup':
          alert(_object);
          break;
        case 'console':
        default:
          console.log(_object);
          break;
      }
    },

    bind: function(context, func) {
      return function() {
        return func.apply(context || window);
      };
    },

    load: function(func, arg) {
      try {
        func(arg);
      } catch (x) {
        //
      }
    }
  };
})();
