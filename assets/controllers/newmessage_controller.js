import { Controller } from 'stimulus';

export default class extends Controller {
  static values = {
    topic: String,
  };

  initialize() {
    this.eventSource = null;
  }

  connect() {
    this.eventSource = new EventSource('http://localhost:3000/.well-known/mercure?topic=' + encodeURIComponent(this.topicValue));
    this.eventSource.onmessage = event => {
      // Will be called every time an update is published by the server
      this.element.innerHTML += event.data;
    };
  }

  disconnect() {
    this.eventSource.close();
  }
}
