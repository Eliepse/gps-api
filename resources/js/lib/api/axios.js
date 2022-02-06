import Axios from "axios";

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
export const api = Axios.create({
	baseURL: "/api/",
	headers: {
		"X-Requested-With": "XMLHttpRequest",
	},
	withCredentials: true,
});
