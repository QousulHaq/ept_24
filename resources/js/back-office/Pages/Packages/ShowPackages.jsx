import React, { useEffect } from 'react'

import "../../../../../public/css/back-office/details-packages.css"

const ShowPackages = ({ package: package_detail, items, intros, subpackage, scheduledExams }) => {

    useEffect(() => {
        console.log(package_detail)
    }, [])

    return (
        <>
            <div className="content-details-package">
                <div className="title-detail-package-wrapper mt-4">
                    <div className="title-group-one">
                        <h3 className="title-detail-package text-black m-0">
                            {package_detail?.title}
                        </h3>
                        <p className="desc-detail-package m-0">
                            {package_detail?.description}
                        </p>
                        <hr className='m-0' />
                    </div>
                    <div className="title-group-two">
                        <h6 className='text-black mb-2'>Category</h6>
                        <div className="category-button-wrapper">
                            {package_detail?.children?.map((item, index) => (
                                <button key={index}>{item?.title}</button>
                            ))}
                        </div>
                        <div className="question-code mt-2">
                            <p className='m-0'>{package_detail?.code}</p>
                            <hr className='m-0' />
                        </div>
                    </div>
                </div>
                <div className="questions-package mt-5">
                    <div className="side-section-category">
                        {
                            package_detail?.children?.filter(item => item.title === 'Listening')[0].children.map((data, index) => (
                                <button key={index}>{data?.title}</button>
                            ))
                        }
                        <button><p className='m-0 p-0'><b>+</b> Add Section</p></button>
                    </div>
                    <div className="question-section">
                        a
                    </div>
                    <div className="add-question-tools">
                        <button className='pl-2 py-2'><p className='mr-2'><b>+</b></p><p>Add Section</p></button>
                        <button className='pl-2 py-2'><i className="fas fa-regular fa-image mr-2"></i><p>Add Image</p></button>
                        <button className='pl-2 py-2'><i className="fas fa-regular fa-file-audio mr-2"></i><p>Add Audio</p></button>
                        <button className='pl-2 py-2'><i className="fas fa-solid fa-file-import mr-2"></i><p>Import Question</p></button>
                    </div>
                </div>
            </div>
        </>
    )
}

export default ShowPackages