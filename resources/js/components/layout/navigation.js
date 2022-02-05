import styles from "./navigation.module.scss";
import { NavLink } from "react-router-dom";

const Navigation = () => {
	function logout() {
		axios.post("/logout").then(() => location.reload())
	}
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
				<li>
					<div className={styles.menuItem} onClick={logout}>Logout</div>
				</li>
			</ul>
		</div>
	);
};

export default Navigation;
