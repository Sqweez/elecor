import Vue from 'vue';

Vue.filter('money', value => {
    return `${new Intl.NumberFormat('ru-RU').format(value)} ₸`;
});
