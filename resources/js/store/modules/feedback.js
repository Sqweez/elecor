import GETTERS from "../getters";
import MUTATIONS from "../mutations";
import ACTIONS from "../actions";
import {changeFeedbackStatus, getFeedback} from "../../api/feedback";

const feedbackModule = {
    state: {
        feedback: null
    },
    getters: {
        [GETTERS.FEEDBACK](state) {
            return state.feedback;
        }
    },
    mutations: {
        [MUTATIONS.SET_FEEDBACKS] (state, payload) {
            state.feedback = payload;
        },
        [MUTATIONS.SET_FEEDBACK] (state, payload) {
            state.feedback = state.feedback.map(feedback => {
               if (payload.id === feedback.id) {
                   feedback.is_worked = true;
                   feedback.answer = payload.answer;
               }
               return feedback;
            });
        }
    },
    actions: {
        async [ACTIONS.GET_FEEDBACK] ({commit}) {
            const feedback = await getFeedback();
            await commit(MUTATIONS.SET_FEEDBACKS, feedback);
        },
        async [ACTIONS.CHANGE_FEEDBACK_STATUS] ({commit}, payload) {
            await changeFeedbackStatus(payload);
            await commit(MUTATIONS.SET_FEEDBACK, payload);
        }
    }
};

export default feedbackModule;
