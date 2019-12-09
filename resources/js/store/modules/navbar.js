const navbarModule = {
    state: {
        menuItems: [
            {
                title: 'Клиентская база',
                icon: 'mdi-account-group',
                url: '/clients',
                hasDropDown: false,
            },
            {
                title: 'Экономические показатели',
                icon: 'mdi-cash-multiple',
                content: [
                    {
                        title: 'Дебиторская задолженность',
                        url: '/receivables',
                    },
                    {
                        title: 'Статистические данные',
                        url: '/stats',
                    }
                ]
            },
            {
                title: 'Управление',
                icon: 'mdi-settings-outline',
                content: [
                    {
                        title: 'Обратная связь',
                        url: '/orders',
                    },
                    {
                        title: 'Содержание страниц',
                        url: '/page-content',
                    },
                    {
                        title: 'Акции',
                        url: '/news',
                    }
                ]
            },
            {
                title: 'Сервисное меню',
                icon: 'mdi-face-agent',
                content: [
                    {
                        title: 'Прайс-лист',
                        url: '/prices',
                    },
                    {
                        title: 'Пользователи',
                        url: '/users',
                    },
                    {
                        title: 'Поля клиента',
                        url: '/client-fields',
                    },
                    {
                        title: 'Рассылка',
                        url: '/mailing',
                    }
                ]
            },
        ]
    },
    getters: {
        menuItems(store) {
            return store.menuItems;
        }
    }
};

export default navbarModule;
