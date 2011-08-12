/**
 * DOM handling
 */
CX.provide('DOM', {
  append: function(node, data) {
    node.appendChild(data);
  },

  clearContent: function(node) {
    while (node.firstChild) {
      node.removeChild(node.firstChild);
    }
  },

  setHTMLContent: function(node, data) {
    node.innerHTML = data;
  }
});

/**
 * Get the a node from an ID
 *
 * @param  id     ID that we have to lookup on the document
 * @return node   node where the ID was found.
 */
CX.$ = function(id) {
  var node = document.getElementById(id);
  if (!node || (node.id != id)) {
    throw new Error(
      'The given ID is not available.');
  }
  return node;
};


/**
 * Set an attribute on a node.
 *
 * @param  node   target node.
 * @param  attr   attribute to be added.
 * @param  value  attribute value.
 * @return bool   If the attribute was changed TRUE, else FALSE.
 */
CX.$A = function(node, attribute, value) {
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
};
