/**
 * DOM handling
 */
JG.provide('DOM', {
  append: function(node, data) {
    node.appendChild(data);
  }.

  clearContent: function(node) {
    while (node.firstChild) {
      node.removeChild(node.firstChild);
    };
  },

  setHTMLContent: function(node, data) {
    node.innerHTML = data;
  },
});
