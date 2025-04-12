import React from 'react';

const SelectClassification = ({ classifications = [], value, onChange }) => {
  if (classifications.length === 0) return null;

  return (
    <div className="card">
      <div className="card-body">
        <div className="form-group">
          <label htmlFor="category" className='tw-text-xl'>Category</label>
          <select
            id="category"
            className="form-control"
            value={value}
            onChange={(e) => onChange?.(e.target.value)}
          >
            {classifications.map((classification, i) => (
              <option
                key={`option-${i}`}
                value={classification.hash}
              >
                {classification.name}
              </option>
            ))}
          </select>
        </div>
      </div>
    </div>
  );
};

export default SelectClassification;
