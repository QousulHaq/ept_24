import React from 'react';

const LineCount = ({ context = { items: [] } }) => {
  return (
    <div id="line-count">
      {context.items.map((style, i) => (
        <div key={`line-count-${i}`} style={style}>
          {i + 1}
        </div>
      ))}
    </div>
  );
};

export default LineCount;

const styles = {
  lineCount: {
    position: 'absolute',
    width: '18px',
  },
  lineCountDiv: {
    width: '18px',
    color: '#f1f3fe',
    fontSize: 'small',
    textAlign: 'right',
  },
};