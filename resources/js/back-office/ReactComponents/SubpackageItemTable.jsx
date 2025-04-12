import React, { useEffect, useState } from 'react'
import { Link } from '@inertiajs/inertia-react'
import Axios from 'axios'
import Swal from 'sweetalert2'

const SubpackageItemTable = ({ items, package_id, subpackage_id, categories = [] }) => {

    const [loading, setLoading] = useState(false)
    const [itemsData, setItemsData] = useState(items)

    const handleDeleteItem = (item_id) => {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Axios.delete(`/api/back-office/package/${subpackage_id}/item/${item_id}/destroy`, {
                    headers: {
                        'Accept': 'application/json',
                    }
                }).then(() => {
                    Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    )
                    setItemsData((prev) => {
                        return prev.filter(item => item.id !== item_id)
                    })
                })
            }
        })
    }

    useEffect(() => {
        console.log({
            items,
            package_id,
            subpackage_id,
            categories
        })
    }, [])

    return (
        <table className="table table-striped table-md">
            <thead>
                <tr>
                    <th>#</th>
                    {
                        itemsData[0]?.category === "" ? "" : <th>Category</th>
                    }
                    <th>Code</th>
                    <th style={{ width: "14em" }}>Last Updated</th>
                    <th style={{ width: "14em" }}></th>
                </tr>
            </thead>
            <tbody>
                {
                    itemsData.length !== 0 ? (
                        itemsData.map((data, index) => (
                            <tr key={index}>
                                <td>{index + 1}</td>
                                {
                                    categories.length !== 0 && <td>{categories.find(category => category.hash === data?.category).name}</td>
                                }
                                <td>{data.code}</td>
                                <td>{new Intl.DateTimeFormat('id-ID', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' }).format(new Date(data?.updated_at))}</td>
                                <td className="text-right">
                                    <Link
                                        href={`/back-office/package/${package_id}/item/${data.id}/item?subpackage=${subpackage_id}`}
                                        className="mr-4"
                                    >
                                        <i className="fas fa-edit alert-warning"></i>
                                    </Link>
                                    {
                                        !Object.keys(itemsData[0]).includes("intro") && (
                                            <button onClick={() => handleDeleteItem(data.id)} type="button">
                                                <i className="fas fa-regular fa-trash tw-text-red-600"></i>
                                            </button>
                                        )
                                    }
                                </td>
                            </tr>
                        ))
                    ) : (
                        <tr>
                            <td colSpan="4" className="text-center">There is no questions</td>
                        </tr>
                    )
                }
            </tbody>
        </table>
    )
}

export default SubpackageItemTable