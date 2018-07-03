data = {
    auth: {
        login: 'name@mail.ru',
        pass: 'password123'
    },
    data: {
        email: 'friend@mail.ru',
        subject: 'may dear friend',
        message: 'this is little message for you {$love_you}',
        files: [
            {
                file: '/path/to/file/love.png',
                type: 'inline',
                name: 'love_you'
            },
            {
                file: '/path/to/other/file/this_is_little_virus.zip'
            }
        ]
    }
};