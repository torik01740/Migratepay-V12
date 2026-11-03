import React from 'react';
import { createRoot } from 'react-dom/client';
import './bootstrap';
import App from './app/App';

// Support multiple mounting targets for maximum backward and forward compatibility
const findMountContainer = () => {
    if (typeof document === 'undefined') return null;
    return (
        document.getElementById('app') ||
        document.getElementById('root') ||
        document.getElementById('user-root') ||
        document.getElementById('admin-root') ||
        document.getElementById('chakra-root')
    );
};

const container = findMountContainer();

if (container) {
    const portal =
        container.getAttribute('data-portal') ||
        (container.id === 'admin-root'
            ? 'admin'
            : container.id === 'user-root'
            ? 'user'
            : null);

    const root = createRoot(container);
    root.render(<App portal={portal} />);
}

export { App };
export default App;

