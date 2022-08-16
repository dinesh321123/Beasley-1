import React, { useEffect, useState } from 'react';
import Cookies from 'universal-cookie';
import Modal from '../library/Modal';

const ScreenInactive = () => {
	const cookies = new Cookies();
	const [show, setShow] = useState(false);
	const [modalData, setModalData] = useState([]);
	const [error, setError] = useState(null);
	const [imgData, setImgData] = useState([]);
	const expEngineApi = `https://experience-dev.bbgi.com/admin/publishers/BBGI/modaldefaults?appkey=63E3E16B-B4FA-440B-8D80-731CB86D4147`;

	const modalClose = () => {
		setShow(false);
		const expDate = new Date();
		expDate.setDate(new Date() + 30);
		cookies.set('modalLastShown', Date(), {
			path: '/',
			expires: new Date(expDate),
		});
	};

	useEffect(() => {
		fetch(expEngineApi)
			.then(response => {
				if (!response.ok) {
					throw new Error(
						`This is an HTTP error: The status is ${response.status}`,
					);
				}
				return response.json();
			})
			.then(modalContent => {
				setModalData(modalContent);
				fetch(modalContent.url)
					.then(res => res.json())
					.then(
						res => {
							if (res.error) {
								setError({ code: res.error, message: res.message });
							} else {
								const keys = Object.keys(res.data);
								const randIndex = Math.floor(Math.random() * keys.length);
								const randKey = keys[randIndex];
								setImgData(res.data[randKey]);
							}
						},
						error => {
							setError(error);
						},
					);
			})
			.catch(err => {
				console.log(err.message);
			});
	}, []);

	useEffect(() => {
		console.log(modalData);
		let idleInterval;
		document.addEventListener('visibilitychange', handleVisibilityChange, true);
		function handleVisibilityChange() {
			if (document.hidden) {
				clearInterval(idleInterval);
			} else {
				let idleTime = 0;
				const resetIdleTime = function() {
					idleTime = 0;
				};
				const checkIfIdle = function() {
					idleTime += 1000;
					console.log(idleTime);
					if (idleTime >= modalData.inactivetime) {
						if (cookies.get('modalLastShown') !== undefined) {
							console.log('in');
							const seconds = Math.abs(
								(new Date().getTime() -
									new Date(cookies.get('modalLastShown')).getTime()) /
									1000,
							);
							if (seconds >= modalData.donotshow) {
								setShow(true);
								clearInterval(idleInterval);
							} else {
								clearInterval(idleInterval);
							}
						} else {
							console.log('out');
							setShow(true);
							clearInterval(idleInterval);
						}
					}
				};
				document.addEventListener('mousemove', resetIdleTime, false);
				document.addEventListener('keypress', resetIdleTime, false);

				idleInterval = setInterval(checkIfIdle, 1000);
			}
		}
	});
	return (
		<div>
			{error ? (
				<div>
					Error: {error.code} - {error.message}
				</div>
			) : (
				<div>
					<button onClick={() => setShow(true)} type="button">
						Show Modal
					</button>
					<Modal
						id="modal"
						title="Sample Screen Inactive Modal"
						onClose={() => modalClose()}
						show={show}
					>
						<img src={imgData.image_url} alt="" />
						<p>{imgData.title}</p>
					</Modal>
				</div>
			)}
		</div>
	);
};
export default ScreenInactive;
