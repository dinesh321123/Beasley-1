import React, {useEffect, useState} from "react";
import Modal from "./Modal/Modal";
import Cookies from 'universal-cookie';


const ScreenInactive = () => {
    const cookies = new Cookies();
     const [show, setShow] = useState(false);
     const [modalData, setModalData] = useState([]);
    const [error, setError] = useState(null);
    const [imgData, setImgData] = useState([]);

    const modalClose = () => {
        setShow(false);
        var expDate = new Date();
        expDate.setDate(new Date()+30);
        cookies.set('modalLastShown', Date(),{ path: '/', expires: (new Date(expDate)) });
    }

    useEffect(() => {
        fetch(`https://experience-dev.bbgi.com/admin/publishers/BBGI/modaldefaults?appkey=63E3E16B-B4FA-440B-8D80-731CB86D4147`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(
                        `This is an HTTP error: The status is ${response.status}`
                    );
                }
                return response.json();
            })
            .then((modalContent) => {
                setModalData(modalContent);
                fetch(modalContent.url)
                    .then(res => res.json())
                    .then(
                        (res) => {
                             if (res.error) {
                                setError({code: res.error, message: res.message});
                            } else{
                                 const keys = Object.keys(res.data);
                                 const randIndex = Math.floor(Math.random() * keys.length);
                                 const randKey = keys[randIndex];
                                 setImgData(res.data[randKey]);
                             }
                        },
                        (error) => {
                            setError(error);
                        }
                    )
            })
            .catch((err) => {
                console.log(err.message);
            });
    }, []);

    useEffect(() => {
        let idleInterval;
        document.addEventListener("visibilitychange", handleVisibilityChange, true);
        function handleVisibilityChange() {
            if(document.hidden) {
                clearInterval(idleInterval);
            }
            else{
                let idleTime = 0;
                document.addEventListener('mousemove', resetIdleTime, false);
                document.addEventListener('keypress', resetIdleTime, false);
                function resetIdleTime()
                {
                    idleTime = 0
                }
                idleInterval = setInterval(checkIfIdle, 1000);

                function checkIfIdle() {
                    idleTime += 1000
                    console.log(idleTime);

                    if (idleTime >= modalData.inactivetime) {
                        if(cookies.get('modalLastShown') !== undefined){
                            var seconds =  Math.abs((new Date().getTime() - new Date(cookies.get('modalLastShown')).getTime()) / 1000);
                            if (seconds >= modalData.donotshow) {
                                setShow(true);
                                clearInterval(idleInterval);
                            } else{
                                clearInterval(idleInterval);
                            }
                        }else{
                            setShow(true);
                            clearInterval(idleInterval);
                        }

                    }
            }
        }
    }
    });
    if (error) {
        return <div>Error: {error.code} - {error.message}</div>;
    } else {
return (
    <div>
        <Modal title="Sample Screen Inactive Modal" onClose={() => modalClose()} show={show}>
            <img src={imgData.image_url}></img>
            <p>{imgData.title}</p>

        </Modal>
    </div>
); }
}
export default ScreenInactive;
