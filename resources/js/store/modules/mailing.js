import {
    createMailingTemplate,
    deleteMailingTemplate,
    editMailingTemplate,
    getMailingTemplates
} from "../../api/mailing";

const mailingModule = {
    state: {
        mailing_templates: [],
    },
    getters: {
        mailing_templates: state => state.mailing_templates,
        mailing_template: state => id => state.mailing_templates.find(m => m.id === id)
    },
    mutations: {
        setMailingTemplates(state, payload) {
            state.mailing_templates = payload;
        },
        deleteMailingTemplate(state, payload) {
            state.mailing_templates = state.mailing_templates.filter(m => m.id !== payload);
        },
        editMailingTemplates(state, payload) {
            state.mailing_templates = state.mailing_templates.map(m => {
                if (m.id === payload.id) {
                    m = payload;
                }
                return m;
            })
        },
        createMailingTemplates(state, payload) {
            state.mailing_templates.push(payload);
        }
    },
    actions: {
        async getMailingTemplates({commit}, payload) {
            const templates = await getMailingTemplates();
            commit('setMailingTemplates', templates);
        },
        async deleteMailingTemplate({commit}, payload) {
            await deleteMailingTemplate(payload);
            commit('deleteMailingTemplate', payload);
        },
        async editMailingTemplates({commit}, payload) {
            const template = await editMailingTemplate(payload);
            commit('editMailingTemplates', template);
        },
        async createMailingTemplates({commit}, payload) {
            const template = await createMailingTemplate(payload);
            commit('createMailingTemplates', template);
        }
    }
};

export default mailingModule;
