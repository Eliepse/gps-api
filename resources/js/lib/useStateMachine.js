import { useRef, useCallback, useState } from "react";
import { interpret } from "robot3";

/**
 *
 * @param {Object} machine - The state machine to use
 * @param {Object} initialContext
 * @param {Boolean|String} debug - Print state changes to the console. A string can be set to name the machine
 * @returns {{
 *    can: (function(String): boolean),
 *    context: Object,
 *    state: String,
 *    send: (function(String, Object=): void),
 *    isState: (function(...String): Boolean)
 * }}
 */
export function useStateMachine(machine, initialContext = {}, debug = false) {
	// Create a new instance of the machine
	const machineRef = useRef(null);
	const debugPrefix = debug === true ? "[Machine] " : `[Machine][${debug}] `;

	/*
	 | -------------------------------------------------------------------------
	 | Initialization
	 | -------------------------------------------------------------------------
	 */

	if (machineRef.current === null) {
		// noinspection JSValidateTypes
		machineRef.current = interpret(
			machine,
			() => {
				// When the machine's state changes, we update our
				// React hook. Otherwise no re-render occurs.
				const currentState = service.machine.current;

				// Update the state and the context all at once
				// to prevent multiple re-renders
				setMachineState({
					state: currentState,
					context: service.context,
				});

				if (debug) {
					console.debug(`${debugPrefix}${currentState}`);
				}
			},
			initialContext
		);

		// Debug initial state
		if (debug) {
			console.debug(`${debugPrefix}${machineRef.current.machine.current}`);
		}
	}

	/*
	 | -------------------------------------------------------------------------
	 | States
	 | -------------------------------------------------------------------------
	 |
	 | This part allows to store with React's hooks elements that will be
	 | updated. Otherwise, it won't trigger a re-render.
	 |
	 */

	const service = machineRef.current;

	// Store the context and the machine's state with react hooks
	// They are contained in a single object to prevent multiple re-renders
	// by setting the machine's state and context separatly.
	const [machineState, setMachineState] = useState({
		state: service.machine.current,
		context: service.context,
	});

	// We spread the state and context for ease of use
	const { state, context } = machineState;

	/*
	 | -------------------------------------------------------------------------
	 | Helpers
	 | -------------------------------------------------------------------------
	 */

	/**
	 * Helper to change state
	 * @type {function(String, Object): void}
	 */
	const send = useCallback(
		function (type, params = {}) {
			if (debug) {
				console.debug(`${debugPrefix}${service.machine.current}(${type})`);
			}

			service.send({ type, data: params });
		},
		[service, debugPrefix, debug]
	);

	/**
	 * Helper to check if the current state can transition to an other state
	 *
	 * @type {function(String): boolean}
	 */
	const can = useCallback(
		(transitionName) => {
			const transitions = service.machine.state.value.transitions;

			// Abort if no transition matches
			if (!transitions.has(transitionName)) {
				return false;
			}

			// We check if the mathing transitions allows to pass to the desired
			// state, by check the associated guards.
			const transitionsForName = transitions.get(transitionName);
			for (const t of transitionsForName) {
				if ((t.guards && t.guards(service.context)) || !t.guards) {
					return true;
				}
			}
			return false;
		},
		[service.context, service.machine.state.value.transitions]
	);

	/**
	 * Helper to check the current state
	 *
	 * @param {...String} states
	 * @returns {boolean}
	 */
	const isState = (...states) => {
		if (!Array.isArray(states)) {
			return false;
		}

		return states.includes(state);
	};

	return { state, context, send, can, isState };
}
