import React, { useContext, useEffect } from "react";
import { ExamContext } from "../perform/ExamContext";
import axios from "axios";

const ExamComponent = () => {
  const { state, dispatch } = useContext(ExamContext);

  useEffect(() => {
    dispatch({ type: "CHANGE_STATUS", payload: "fetching" });
    axios
      .get("/api/client/section")
      .then((response) => {
        dispatch({ type: "CHANGE_MATTER", payload: response.data });
        dispatch({ type: "CHANGE_STATUS", payload: "idle" });
      })
      .catch(() => {
        dispatch({ type: "CHANGE_STATUS", payload: "error" });
      });
  }, [dispatch]);

  const handleAnswer = (sectionId, itemId, answerId) => {
    dispatch({ type: "UPDATE_ATTEMPT", payload: { sectionId, itemId, attempt: { answer: answerId } } });
    axios.post("/api/client/section/item/attempt", {
      participant_section: sectionId,
      section_item: itemId,
      item_answer_id: answerId,
    });
  };

  return (
    <div>
      <h1>Ujian</h1>
      {state.matter.sections.map((section) => (
        <div key={section.id}>
          <h2>{section.title}</h2>
          {section.items.map((item) => (
            <div key={item.id}>
              <p>{item.question}</p>
              {item.answers.map((answer) => (
                <button key={answer.id} onClick={() => handleAnswer(section.id, item.id, answer.id)}>
                  {answer.text}
                </button>
              ))}
            </div>
          ))}
        </div>
      ))}
    </div>
  );
};

export default ExamComponent;
