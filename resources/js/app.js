import "./bootstrap";
import { render } from "react-dom";
import { Root } from "components/Root";
import { store } from "store/store";
import { Provider } from "react-redux";
import { BrowserRouter } from "react-router-dom";

const app = document.querySelector("#app");

if (app) {
	render(
		<BrowserRouter basename="/dashboard">
			<Provider store={store}>
				<Root />
			</Provider>
		</BrowserRouter>,
		app
	);
}
