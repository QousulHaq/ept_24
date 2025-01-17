import React, { useEffect, useState } from 'react'
import { Link } from '@inertiajs/inertia-react'

import SubpackageItemTable from '../../ReactComponents/SubpackageItemTable'

import "../../../../../public/css/back-office/details-packages.css"

const ShowPackages = ({ package: package_detail, items, intros, subpackage, scheduledExams, message }) => {

    const [subPackageName, setSubPackageName] = useState(subpackage ? subpackage.title : "")

    useEffect(() => {
        console.log("ini package detail :", package_detail)
        console.log("ini items :", items)
        console.log("ini intros :", intros)
        console.log("ini subpackage :", subpackage)
        console.log("ini schedule exams :", scheduledExams)
        console.log("message :", message)
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
                                <Link
                                    href={`/back-office/package/${package_detail?.id}?subpackage=${item.children.length === 0 ? item.id : item.children[0].id}`}
                                    key={index}
                                    style={
                                        item.title === subPackageName || item.children.some(item => item.title === subPackageName) ?
                                            {
                                                backgroundColor: "#2B7FD4",
                                                color: "white"
                                            }
                                            :
                                            {}
                                    }
                                >{item?.title}</Link>
                            ))}
                        </div>
                        {/* <div className="question-code mt-2">
                            <p className='m-0'>{package_detail?.code}</p>
                            <hr className='m-0' />
                        </div> */}
                    </div>
                </div>
                <div className="questions-package mt-5">
                    <div className="side-section-category">
                        {
                            subpackage.title !== "Reading" ? (
                                package_detail?.children?.filter(item => item.title === subPackageName || item.children.some(item => item.title === subPackageName))[0].children.map((data, index) => (
                                    <Link
                                        href={`/back-office/package/${package_detail?.id}?subpackage=${data.id}`}
                                        className='mb-1'
                                        key={index}
                                        style={
                                            data.title === subPackageName ?
                                                {
                                                    backgroundColor: "#2B7FD4",
                                                    color: "white"
                                                }
                                                :
                                                {}
                                        }
                                    >{data.title}</Link>
                                ))
                            ) : (
                                <Link
                                    href={`/back-office/package/${package_detail?.id}?subpackage=${subpackage.id}`}
                                    className='mb-1'
                                    style={
                                        {
                                            backgroundColor: "#2B7FD4",
                                            color: "white"
                                        }
                                    }
                                >Reading</Link>
                            )
                        }
                        {/* <button><p className='m-0 p-0'><b>+</b> Add Section</p></button> */}
                    </div>
                    <div className="package-items-section">
                        <div className="introduction-table mb-3">
                            <h6>Introduction</h6>
                            <SubpackageItemTable items={intros} package_id={package_detail.id} subpackage_id={subpackage.id}/>
                        </div>
                        <div className="package-items-table">
                            <h6>{subpackage.title}</h6>
                            <SubpackageItemTable items={items.data} package_id={package_detail.id} subpackage_id={subpackage.id}/>
                        </div>
                    </div>
                    {/* <div className="add-question-tools">
                        <button className='pl-2 py-2'><p className='mr-2'><b>+</b></p><p>Add Section</p></button>
                        <button className='pl-2 py-2'><i className="fas fa-regular fa-image mr-2"></i><p>Add Image</p></button>
                        <button className='pl-2 py-2'><i className="fas fa-regular fa-file-audio mr-2"></i><p>Add Audio</p></button>
                        <button className='pl-2 py-2'><i className="fas fa-solid fa-file-import mr-2"></i><p>Import Question</p></button>
                    </div> */}
                </div>
            </div >
        </>
    )
}

export default ShowPackages