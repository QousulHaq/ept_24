// QuizBuilder.jsx
import React from 'react';
import { Radio } from 'antd';
import Bundle from '../builder/Bundle';
import MultiChoiceSingle from '../builder/MultiChoiceSingle';

const { Group: RadioGroup, Button: RadioButton } = Radio;

const QuizBuilder = ({ item, disabled = false, value = '', onChange, onCountdownFreezeChange }) => {
    if (!item) return null;

    const answerChange = (e, itemId = null) => {
        onChange?.({ value: e.target.value, itemId });
    };

    const multipleChoiceValue = (itemParam = null, valueParam = null) => {
        const currentItem = itemParam ?? item;
        const currentValue = valueParam ?? value;

        return (
            currentItem?.answers?.find((a) => a.content === currentValue)?.id ?? ''
        );
    };

    if (item.type === 'bundle') {
        return (
            <Bundle item={item} onCountdownFreezeChange={onCountdownFreezeChange}>
                {({ item: subItem, value: subItemValue }) => (
                    <MultiChoiceSingle
                        item={subItem}
                        disablePlugin={subItem.id !== item.id}
                        musicIcon={{ width: 60, height: 40, hidden: true }}
                        onCountdownFreezeChange={onCountdownFreezeChange}
                        className="mt-8"
                    >
                        <RadioGroup
                            onChange={(e) => answerChange(e, subItem.id)}
                            value={multipleChoiceValue(subItem, subItemValue)}
                            disabled={disabled}
                            className="w-full"
                        >
                            {subItem.answers.map((answer, i) => (
                                <RadioButton
                                    key={answer.id}
                                    value={answer.id}
                                    className="block my-2 h-auto text-black"
                                >
                                    {`${String.fromCharCode(65 + i)}. ${answer.content}`}
                                </RadioButton>
                            ))}
                        </RadioGroup>
                    </MultiChoiceSingle>
                )}
            </Bundle>
        );
    }

    if (item.type === 'multi_choice_single') {
        return (
            <MultiChoiceSingle
                item={item}
                onCountdownFreezeChange={onCountdownFreezeChange}
            >
                <RadioGroup
                    onChange={(e) => answerChange(e)}
                    value={multipleChoiceValue()}
                    disabled={disabled}
                    className="w-full"
                >
                    {item.answers.map((answer, i) => (
                        <RadioButton
                            key={answer.id}
                            value={answer.id}
                            className="block my-2 h-auto text-black"
                        >
                            {`${String.fromCharCode(65 + i)}. ${answer.content}`}
                        </RadioButton>
                    ))}
                </RadioGroup>
            </MultiChoiceSingle>
        );
    }

    return null;
};

export default QuizBuilder;