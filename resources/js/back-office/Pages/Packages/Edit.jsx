import React, { useEffect } from 'react'
import { useForm } from '@inertiajs/inertia-react'

const Edit = ({ package: package_data }) => {
  const { data, setData, put, processing, errors } = useForm({
    title: package_data.title,
    description: package_data.description,
    level: package_data.level,
  })

  const submitForm = (e) => {
    e.preventDefault()
    put(`/back-office/package/${package_data?.id}`)
  };

  useEffect(() => {
    console.log(package_data)
  }, [])

  return (
    <div className="card">
      <div className="card-body">
        <div className="row">
          <div className="col-md-5">
            <form onSubmit={(e) => submitForm(e)}>
              <div className="form-group">
                <label htmlFor="title">Title</label>
                <input name="title" id="title" type="text" className="form-control" onChange={(e) => setData('title', e.target.value)} value={data.title} />
                {errors.title && <p style={{ color: "red" }}>{errors.title}</p>}
              </div>
              <div className="form-group">
                <label htmlFor="description">Description</label>
                <textarea name="description" id="description" className="form-control" style={{ height: "60px" }} onChange={(e) => setData('description', e.target.value)} value={data.description}></textarea>
                {errors.description && <p style={{ color: "red" }}>{errors.description}</p>}
              </div>
              <div className="form-group">
                <label htmlFor="level">Level</label>
                <input type="number" name="level" className="form-control" id="level" min="1" onChange={(e) => setData('level', e.target.value)} value={data.level} />
                {errors.level && <p style={{ color: "red" }}>{errors.level}</p>}
              </div>
              <button type="submit" className="btn btn-dark" disabled={processing}>{processing ? "Processing" : "Save"}</button>
              <button type="reset" className="btn btn-default">Reset</button>
            </form>
          </div>
        </div>
      </div>
    </div>

  )
}

export default Edit