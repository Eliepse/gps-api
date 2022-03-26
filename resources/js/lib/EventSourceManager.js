class EventSourceManager {
	topics = [];

	_es;

	_listeners = {
		subscription: [],
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

		console.debug(type, data);

		this._listeners[type].forEach((listener) => listener(data));
	}

	addListener(type, callback) {
		if (!Object.keys(this._listeners).includes(type)) {
			return;
		}

		this._listeners[type].push(callback);
	}

	removeListener(type, callback) {
		if (!Object.keys(this._listeners).includes(type)) {
			return;
		}

		this._listeners[type] = this._listeners[type].filter((listener) => listener !== callback);
	}
}

export const Mercure = new EventSourceManager();