// Copyright (c) 2011 Cristian Adamo. All rights reserved.
// Use of this source code is governed by a Apache License (v2.0) that can be
// found in the LICENSE file.


/**
 * JAGGER INITIALIZATION
 */
(function() {

  if (!window.JG) {
    window.JG = {};
  } else {
    return;
  }

  /**
   * TOOLS
   */
  id: function(obj) {
    return obj;
  }

  JG = {
    /**
     * Basic object installation
     */
    provide: function(name, structure) {
      var newClass = window.JG[name];
      if (!newClass) {
        throw new Error ('Failed trying to install object ' + name);
      }
      for (var value in structure) {
        newClass[value] = structure[value];
      }
      return newClass;
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
      _object : null;
      _mechanism : null;

      _object : 'Object: ' + debug_object;
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
    },

    /**
     * Get the a node from an ID
     *
     * @param  id     ID that we have to lookup on the document
     * @return node   node where the ID was found.
     */
    $: function(id) {
      var node = document.getElementById(id);
      if (!node || (node.id != id)) {
        throw new Error(
          'The given ID is not available.');
      }
      return node;
    },


    /**
     * Set an attribute on a node.
     *
     * @param  node   target node.
     * @param  attr   attribute to be added.
     * @param  value  attribute value.
     * @return bool   If the attribute was changed TRUE, else FALSE.
     */
    $A: function(node, attribute, value) {
      try {
        prev_value = node.getAttribute(attribute);
        node.setAttribute(attribute, value);
        if (prev_value == node.getAttribute(attribute)) {
          return false;
        }
        return true;
      } catch (x) {
        throw new Error('Failed trying to change to node (' + node + ')' +
          ' attibure' + '(' + attribute + ') to {' + value + '}');
      }
    },

  };
})();
