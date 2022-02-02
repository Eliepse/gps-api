import styles from "./navigation.module.scss";
import { NavLink } from "react-router-dom";

const Navigation = () => {
	return (
		<div className={styles.root}>
			<ul className={styles.nav}>
				<li>
					<NavLink to="/">
						<div className={styles.menuItem}>Home</div>
					</NavLink>
				</li>
				<li>
					<NavLink to="/live">
						<div className={styles.menuItem}>Live</div>
					</NavLink>
				</li>
				<li>
					<NavLink to="/traces">
						<div className={styles.menuItem}>Parcours</div>
					</NavLink>
				</li>
			</ul>
		</div>
	);
};

export default Navigation;
