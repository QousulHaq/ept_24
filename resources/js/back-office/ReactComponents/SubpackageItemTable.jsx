import React, { useEffect } from 'react'
import { Link } from '@inertiajs/inertia-react'

const SubpackageItemTable = ({ items, package_id, subpackage_id }) => {
    useEffect(() => {
        console.log(items)
    }, [])
    return (
        <table className="table table-striped table-md">
            <thead>
                <tr>
                    <th>#</th>
                    {
                        items[0]?.category === "" ? "" : <th>Category</th>
                    }
                    <th>Code</th>
                    <th style={{ width: "14em" }}>Last Updated</th>
                    <th style={{ width: "14em" }}></th>
                </tr>
            </thead>
            <tbody>
                {
                    items.length !== 0 ? (
                        items.map((data, index) => (
                            <tr key={index}>
                                <td>{index + 1}</td>
                                {
                                    data?.category && <td>{data?.category}</td>
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
                                </td>
                            </tr>
                        ))
                    ) : (
                        <tr>
                            <td colspan="4" className="text-center">There is no questions</td>
                        </tr>
                    )
                }
            </tbody>
        </table>
    )
}

export default SubpackageItemTable