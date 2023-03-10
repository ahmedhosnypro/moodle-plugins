import dom from './dom';
// Actions are the callbacks for things like adding
// new attributes, options, field removal confirmations etc.
// Every Action below can be overridden via module options

// Default options

const defaultActions = {
  add: {
    attr: evt => {
      dom.addAttributePrompt('primary', evt.message.attr, evt.addAction);
    },
    option: evt => {
      evt.addAction();
    }
  },
  click: {
    btn: evt => {
      evt.action();
    }
  },
  save: evt => {
    return true;
  }
};

/**
 * Events class is used to register actions and throttle their callbacks
 */
const actions = {
  init: function(options) {
    this.opts = Object.assign({}, defaultActions, options);
    return this;
  },
  add: {
    attrs: evt => {
      actions.opts.add.attr(evt);
    },
    options: evt => {
      actions.opts.add.option(evt);
    }
  },
  click: {
    btn: evt => {
      actions.opts.click.btn(evt);
    }
  },
  save: evt => {
    return true;
  }
};

export default actions;
