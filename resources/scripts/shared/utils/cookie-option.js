export default {
    admin: {
        expires: 30,
        secure: true,
        sameSite: 'strict',
        path: '/admin',
        domain: window.location.hostname
    },
    merchant: {
        expires: 30,
        secure: true,
        sameSite: 'strict',
        path: '/admin',
        domain: window.location.hostname
    },
    user: {
        expires: 30,
        secure: true,
        sameSite: 'strict',
        path: '/',
        domain: window.location.hostname
    }
}
