import "./bootstrap";
import { render } from "react-dom";
import { Root } from "components/Root";
import { BrowserRouter } from "react-router-dom";

const app = document.querySelector("#app");

if (app) {
	render(
		<BrowserRouter basename="/dashboard">
			<Root />
		</BrowserRouter>,
		app
	);
}
