import { arrWrap } from "lib/supports/array";

class EventSourceManager {
	topics = [];

	_es;

	_listeners = {
		presence: [],
		message: [],
	};

	connect() {
		console.debug("[Mercure] Connecting...");

		const subscribeURL = new URL(process.env.MIX_MERCURE_URL);

		this.topics.forEach((topic) => subscribeURL.searchParams.append("topic", topic));

		this._es = new EventSource(subscribeURL, { withCredentials: true });
		this._es.addEventListener("open", () => console.debug("[Mercure] Connected."));
		this._es.addEventListener("error", () => console.debug("[Mercure] Failed!"));
		this._es.addEventListener("message", (event) => this._handleMessageEvent(event));
	}

	disconnect() {
		this._es.close();
	}

	_handleMessageEvent(event) {
		const data = JSON.parse(event.data);
		const type = (data.type || "message").toLowerCase();

		if ("message" === type) {
			this._listeners.message.forEach(({ events, callback }) => {
				if (events === false) {
					return;
				}

				if (events !== true && !arrWrap(events).includes(data.event)) {
					return;
				}

				callback(data.data, data.event);
			});
		} else if ("subscription" === type) {
			this._listeners.presence.forEach((listener) => listener(data));
		}
	}

	addPresenceListener(callback) {
		this._listeners.presence.push(callback);
	}

	/**
	 *
	 * @param {string|string[]|function(*, string): void} events
	 * @param {function(event): void=} callback
	 */
	addMessageListener(events, callback) {
		if (typeof events === "function") {
			this._listeners.message.push({ events: true, callback: events });
			return;
		}

		this._listeners.message.push({ events, callback });
	}

	removePresenceListener(callback) {
		this._listeners.presence = this._listeners.presence.filter((listener) => listener !== callback);
	}

	removeMessageListener(callback) {
		this._listeners.message = this._listeners.message.filter(({ callback: listener }) => listener !== callback);
	}
}

export const Mercure = new EventSourceManager();
