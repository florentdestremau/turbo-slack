import { Controller } from 'stimulus';

const ACTIONS = {
  append: 'append',
  replace: 'replace',
  remove: 'remove',
  prepend: 'prepend',
};

export default class extends Controller {
  static values = {
    topic: String,
    action: String,
  };

  initialize() {
    this.eventSource = null;
  }

  connect() {
    this.eventSource = new EventSource('http://localhost:3000/.well-known/mercure?topic=' + encodeURIComponent(this.topicValue));
    this.eventSource.onmessage = event => {
      // Will be called every time an update is published by the server
      switch (this.actionValue) {
        case ACTIONS.append:
          this.element.innerHTML += event.data;
          break;
        case ACTIONS.prepend:
          this.element.innerHTML = event.data + this.element.innerHTML;
          break;
        case ACTIONS.replace:
          this.element.innerHTML = event.data;
          break;
        case ACTIONS.remove:
          this.element.innerHTML = null;
          break;
      }
    };
  }

  disconnect() {
    this.eventSource.close();
  }
}
