const ACTIONS = {
    INIT: 'init',
    //CLIENTS
    GET_CLIENTS: 'getClients',
    GET_CLIENT_TYPES: 'getClientTypes',
    CREATE_CLIENT: 'createClient',
    GET_CLIENT: 'getClient',
    EDIT_CLIENT: 'editClient',
    DELETE_CLIENT: 'deleteClient',
    CLEAR_CLIENT: "clearClient",
    //SERVICES
    GET_SERVICES: 'getServices',
    CREATE_SERVICE: 'createService',
    DELETE_SERVICE: 'deleteService',
    EDIT_SERVICE: 'editService',
    //CONNECTIONS
    GET_CONNECTIONS: 'getConnections',
    ADD_CONNECTION: 'addConnection',
    DELETE_CONNECTION: 'deleteConnection',
    DISCONNECT: 'disconnect',
    CONNECT: 'connect',
    ADD_BALANCE: 'addBalance',
    SALE: 'sale',
    GET_ORDERS: 'getOrders',
    GET_FEEDBACK: 'getFeedback',
    CHANGE_ORDER_STATUS: 'changeOrderStatus',
    CHANGE_FEEDBACK_STATUS: 'changeFeedbackStatus',
    GET_STOCKS: 'getStocks',
    CREATE_STOCK: 'createStock',
    DELETE_STOCK: 'deleteStock',
    EDIT_STOCK: 'editStock',
    CHANGE_STOCK_STATUS: 'changeStockStatus',
    GET_MOBILE_SERVICES: 'getMobileServices',
    EDIT_CONNECTION: 'editConnection',
    EDIT_MOBILE_SERVICE: 'editMobileService',
    CREATE_MOBILE_SERVICE: 'createMobileService',
    DELETE_MOBILE_SERVICE: 'deleteMobileService'
};

export default ACTIONS;
