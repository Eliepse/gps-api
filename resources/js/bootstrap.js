import Echo from "laravel-echo";
import { api } from "lib/api/axios";
import dayjs from "dayjs";

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

const relativeTime = require("dayjs/plugin/relativeTime");
const duration = require("dayjs/plugin/duration");
dayjs.extend(relativeTime);
dayjs.extend(duration);

window.Pusher = require("pusher-js");

window.Echo = new Echo({
	broadcaster: "pusher",
	key: process.env.MIX_PUSHER_APP_KEY,
	wsHost: process.env.MIX_PUSHER_HOST,
	wssHost: process.env.MIX_PUSHER_HOST,
	wsPort: process.env.MIX_PUSHER_PORT,
	wssPort: process.env.MIX_PUSHER_PORT,
	cluster: process.env.MIX_PUSHER_APP_CLUSTER,
	forceTLS: process.env.MIX_FORCE_TLS === "true",
	authorizer: (channel, options) => {
		return {
			authorize: (socketId, callback) => {
				api
					.post("/broadcasting/auth", { socket_id: socketId, channel_name: channel.name })
					.then((response) => callback(false, response.data))
					.catch((error) => callback(true, error));
			},
		};
	},
});
