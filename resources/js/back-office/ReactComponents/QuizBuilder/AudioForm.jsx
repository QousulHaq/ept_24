import React, { useEffect, useState } from 'react';
import Axios from 'axios';

const AudioForm = ({ value, onChange }) => {
  const [attachment, setAttachment] = useState({ url: null, id: null });
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (value) {
      setLoading(true);
      Axios.get(`/api/attachment/${value}.json`, {
        headers: {
          'Accept': 'application/json',
        }
      }).then(res => {
        setLoading(false);
        setAttachment(res.data);
      });
    }
  }, [value]);

  useEffect(() => {
    console.log('Ini nilai dari Attachment:', attachment);
  },[attachment])

  const uploadFile = (e) => {
    deleteFile();

    const formData = new FormData();
    formData.append('file', e.target.files[0]);

    Axios.post(`/api/attachment`, formData, {
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'multipart/form-data'
      }
    }).then((res) => {
      setAttachment(res.data);
      onChange(res.data.id);
    }
    ).catch((err) => {
      console.error(err);
    });
  };

  const deleteFile = () => {
    if (attachment.id) {
      Axios.delete(`/api/attachment/${attachment.id}`, {
        headers: {
          'Accept': 'application/json',
        }
      }).then(() => {
        setAttachment({ url: null, id: null });
      });
    }
  };

  return (
    <div id="audio-form">
      <div className="form-group">
        <input
          type="file"
          className="form-control"
          onChange={uploadFile}
        />
        {loading && <p>Loading...</p>}
        {attachment.url && (
          <audio src={attachment.url} controls style={{ width: '100%' }} />
        )}
      </div>
    </div>
  );
};

export default AudioForm;

