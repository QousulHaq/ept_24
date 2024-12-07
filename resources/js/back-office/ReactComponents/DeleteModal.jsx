import React, { useEffect } from 'react'
import { useForm } from '@inertiajs/inertia-react'

const DeleteModal = ({ item, closeDeleteModal }) => {
    const { delete: destroy } = useForm()

    useEffect(() => {
        console.log(item)
    }, [])

    const deleteItem = (e, id) => {
        e.preventDefault()
        destroy(`/back-office/package/${id}`)
    }

    return (
        <div className="delete-modal">
            <div className="modal-dialog" role="document">
                <div className="modal-content">
                    <div className="modal-header">
                        <h5 className="modal-title" id="exampleModalLabel">Delete <span className="text-danger">"{item[0].title}"</span></h5>
                        <button type="button" className="close" onClick={closeDeleteModal}>
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div className="modal-body">
                        Are u sure want to delete this package ?. This action cannot be undone!.
                    </div>
                    <div className="modal-footer">
                        <form onSubmit={(e) => deleteItem(e, item[0].id)}>
                            <button type="button" className="btn btn-secondary mr-3" onClick={closeDeleteModal}>Cancel</button>
                            <button type="submit" className="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    )
}

export default DeleteModal