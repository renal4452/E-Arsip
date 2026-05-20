import React from 'react';

export default function InputField({ label, id, value, onChange, error, placeholder, type = 'text', required = false }) {
    return (
        <div>
            <label htmlFor={id} className="form-label">
                {label} {required && <span className="text-rose-500">*</span>}
            </label>
            <input
                id={id}
                type={type}
                value={value}
                onChange={onChange}
                placeholder={placeholder}
                className={`form-input ${error ? 'form-input-error' : ''}`}
            />
            {error && <p className="form-error-text">{error}</p>}
        </div>
    );
}