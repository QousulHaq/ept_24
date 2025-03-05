import React from 'react';
import Bundle from '../builder/Bundle';
import MultiChoiceSingle from '../builder/MultiChoiceSingle';
import { Radio } from 'antd';

const QuizBuilder = ({ item, disabled = false, value = '', onChange }) => {
    const answerChange = (element, itemId = null) => {
        onChange({ value: element.target.value, itemId });
    };

    const multipleChoiceValue = (item = null, val = null) => {
        return item?.answers.find(a => a.content === (val ?? value))?.id ?? '';
    };

    if (item === null) return null;

    return (
        <div>
            {item.type === 'bundle' ? (
                <Bundle item={item} onCountdownFreezeChange={args => onChange(args)}>
                    {({ item: subItem, value: subItemValue }) => (
                        <MultiChoiceSingle
                            item={subItem}
                            disablePlugin={subItem.id !== item.id}
                            musicIcon={{ width: 60, height: 40, hidden: true }}
                            onCountdownFreezeChange={args => onChange(args)}
                            style={{ marginTop: '2em' }}
                        >
                            <Radio.Group onChange={e => answerChange(e, subItem.id)} value={multipleChoiceValue(subItem, subItemValue)} disabled={disabled}>
                                {subItem.answers.map((answer, i) => (
                                    <Radio.Button key={answer.id} value={answer.id}>
                                        {String.fromCharCode(65 + i)}. {answer.content}
                                    </Radio.Button>
                                ))}
                            </Radio.Group>
                        </MultiChoiceSingle>
                    )}
                </Bundle>
            ) : item.type === 'multi_choice_single' ? (
                <MultiChoiceSingle item={item} onCountdownFreezeChange={args => onChange(args)}>
                    <Radio.Group onChange={e => answerChange(e)} value={multipleChoiceValue()} disabled={disabled}>
                        {item.answers.map((answer, i) => (
                            <Radio.Button key={answer.id} value={answer.id}>
                                {String.fromCharCode(65 + i)}. {answer.content}
                            </Radio.Button>
                        ))}
                    </Radio.Group>
                </MultiChoiceSingle>
            ) : null}
        </div>
    );
};

export default QuizBuilder;