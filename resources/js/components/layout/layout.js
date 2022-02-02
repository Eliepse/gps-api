import PropTypes from "prop-types";
import styles from "./layout.module.scss";
import Navigation from "components/layout/navigation";

const Layout = ({ children }) => {
	return (
		<div className={styles.root}>
			<div className={styles.header}>
				<Navigation />
			</div>
			<div className={styles.body}>
				{children}
			</div>
		</div>
	);
};

Layout.propTypes = {
	children: PropTypes.any
};

export default Layout;