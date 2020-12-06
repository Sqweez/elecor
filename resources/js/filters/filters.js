import Vue from 'vue';

Vue.filter('money', value => `${new Intl.NumberFormat('ru-RU').format(value)} ₸`);

Vue.filter('bonus', value => `${new Intl.NumberFormat('ru-RU').format(value)} бонусов`);

Vue.filter('positive', value => (value < 0) ? value * -1 : value);

Vue.filter('account_pipe', function (_value) {
    let value = _value;
    value = value.split('');
    let output = '';
    value.forEach((item, index) => {
        output += item;
        if (index % 2 !== 0) {
            output += " ";
        }
    });

    return output;
});
