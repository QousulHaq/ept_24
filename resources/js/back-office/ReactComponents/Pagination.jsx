import React from 'react'

const Pagination = ({ links }) => {
    return (
        <nav>
            <ul className="pagination">
                {links.map((link, index) => (
                    <li
                        key={index}
                        className={`page-item ${link.active ? 'active' : ''}`}
                    >
                        <a
                            href={link.url || '#'}
                            className="page-link"
                            onClick={(e) => {
                                if (!link.url) e.preventDefault();
                            }}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        >
                        </a>
                    </li>
                ))}
            </ul>
        </nav>
    )
}

export default Pagination