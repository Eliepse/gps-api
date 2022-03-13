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
