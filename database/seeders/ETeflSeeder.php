<?php

namespace Database\Seeders;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Extra\Presets\ETefl;
use Illuminate\Database\Seeder;
use App\Entities\Media\Attachment;
use App\Entities\Question\Package;
use Illuminate\Support\Collection;
use App\Entities\Question\Package\Item;

class ETeflSeeder extends Seeder
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function run(): void
    {
        $this->faker = Factory::create();

        /**
         * @var $package Package
         */
        $package = Package::query()
            ->where('config', ETefl::NAME)
            ->firstOrFail();

        force_queue_sync(function () use ($package) {
            foreach ($package->children as $child) {
                /**
                 * @var $child Package
                 */
                switch ($child->getRawOriginal('config')) {
                    case 'E-TEFL.reading':
                        $this->command->alert('!!! Start seeder for reading section');
                        $this->createQuestionForReadingSection($child);
                        break;
                    case 'E-TEFL.grammar':
                        $this->command->alert('!!! Start seeder for grammar section');
                        $this->createQuestionForGrammarSection($child);
                        break;
                    case 'E-TEFL.listening':
                        $this->command->alert('!!! Start seeder for listening section');
                        $this->createQuestionForListeningSection($child);
                        break;
                }
            }

            dispatch(new ETefl\Jobs\Package\BuildNoteForPackage($package));
        });
    }

    public function getCategories(Package $package): Collection
    {
        $categories = collect();

        if (! empty($package->config['categories']) && count($package->config['categories']) > 0) {
            $takeEachCategories = floor($package->config['item_total'] / count($package->classifications));
            $residue = $package->config['item_total'] % count($package->classifications);

            for ($i = 0; $i < $takeEachCategories; $i++) {
                $categories = $categories->merge($package->classifications);
            }

            $categories = $categories->merge($categories->take($residue));
        }

        return $categories;
    }

    private function infoCreated(Item $item): void
    {
        $this->command->info($item->getAttribute('code').' has been created!');
    }

    private function generateAnswers(Item $item, $count = 4): void
    {
        /**
         * @var $answers \Illuminate\Database\Eloquent\Collection
         */
        $answers = Item\Answer::factory()
            ->count($count)
            ->create(['item_id' => $item->getAttribute('id')])
            ->each(fn (Item\Answer $answer, $index) => $answer->update(['order' => $index]));

        /* @noinspection PhpUndefinedMethodInspection */
        $answers->random(1)->first->update(['correct_answer' => true]);
    }

    private function createQuestionForReadingSection(Package $package): void
    {
        $config = $package->getAttribute('config');
        $configItem = $config['item'];

        $makeGenerator = function (array $customConfig = []) use ($configItem) {
            $config = array_merge($configItem, $customConfig);

            return function (Item $item, int $index) use ($config) {
                $item->setAttribute(
                    'content',
                    implode(array_map(
                        static fn ($str) => "<p>$str</p>",
                        explode('@@@', chunk_split(implode('', $this->faker->paragraphs(15)), 85, '@@@'))
                    ))
                );
                $item->setAttribute('order', $index);
                $item->fill(Arr::only($config, ['type', 'item_count']));

                $code = $config['code'] ?? 'READING-'.$index;
                $item->setAttribute('code', $code);

                if ($item->save()) {
                    $this->infoCreated($item);

                    Item::factory()->count($config['item_count'])->make()
                        ->each(fn (Item $subItem, int $subIndex) => $subItem->fill(array_merge(
                            ['parent_id' => $item->getAttribute('id')],
                            Arr::only($config['sub-item'], ['duration', 'answer_order_random', 'item_count']),
                            ['order' => $subIndex, 'code' => $code.'-'.$subIndex]
                        )))
                        ->each(fn (Item $subItem) => $subItem->save())
                        ->each(fn (Item $subItem) => $this->infoCreated($subItem))
                        ->each(fn (Item $subItem) => $this->generateAnswers($subItem, Arr::get($config, 'sub-item.answer_count', 4)));
                }
            };
        };

        // create introduction
        $introContent = '<p><strong>SECTION 3 READING COMPREHENSION</strong></p><p><strong>Time-55 minutes</strong></p><p><strong>(including the reading of the directions)</strong></p><p>&nbsp;</p><p><strong><u>Directions</u></strong>: In this section you will read several passages. Each one is followed by a number of questions about it. You are to choose the <strong>one</strong>&nbsp;best answer, (A), (B), (C), or (D), to each question.</p><p>Answer all questions about the information in a passage on the basis of what is <strong>stated</strong>&nbsp;or <strong>implied</strong>&nbsp;in that passage.</p><p>Read the following passage:</p><p></p><p><em>Line        </em>John Quincy Adams, who served as the sixth president of the United States from </p><p>               1825 to 1829, is today recognized for his masterful statesmanship and diplomacy.</p><p>               He dedicated his life to public service, both in the presidency and in the various </p><p>               other political offices that he held. Throughout his political career he </p><p> (5)         demonstrated his unswerving belief in freedom of speech, the antislavery cause, </p><p>               and the right of Americans to be free from European and Asian domination.</p><p>&nbsp;</p><p><strong>Example I</strong></p><p>            To what did John Quincy Adams devote his life?</p><p>            (A) Improving his personal life</p><p>            (B) Serving the public</p><p>            (C) Increasing his fortune</p><p>            (D) Working on his private business</p><p>&nbsp;</p><p>According to the passage, John Quincy Adams "dedicated his life to public service." Therefore, you should choose <strong>(B).</strong></p><p><strong>Example II</strong></p><p>            In line 4, the word "unswerving" is closest in meaning to ___.&nbsp;</p><p>           (A) moveable</p><p>           (B) insignificant</p><p>           (C) unchanging</p><p>           (D) diplomatic</p><p>&nbsp;</p><p>The passage states that John Quincy Adams demonstrated his unswerving belief "throughout his career," This implies that the belief did not change, Therefore, you should choose <strong>(C)</strong>.</p><p>Now begin work on the questions.</p>';

        $package->introductions()->attach(
            Item::factory()
                ->count(1)
                ->create([
                    'item_count' => 1,
                    'code' => $config['intro_code'],
                    'content' => $introContent,
                    'type' => 'bundle',
                ]),
            ['type' => Package::PACKAGE_ITEM_TYPE_INTRO]
        );

        // create items
        $package->items()->syncWithoutDetaching(
            Item::factory()->count(Arr::get($package->config, 'item_total', 5))
                ->make()->each($makeGenerator()));
    }

    private function createQuestionForGrammarSection(Package $package): void
    {
        foreach ($package->children as $child) {
            /**
             * @var $child Package
             */
            $configItem = $child->config['item'];

            $categories = $this->getCategories($child);

            $makeGenerator = function (array $customConfig = []) use ($child, $configItem, &$categories) {
                $config = array_merge($configItem, $customConfig);

                return function (Item $item, int $index) use ($child, $config, &$categories) {
                    if (isset($config['extra'])) {
                        $answers = [];
                        $i = 0;

                        $paragraphs = array_map(
                            static function ($str) use (&$i, &$answers) {
                                if ($i % 2 === 0 && count($answers) <= 4) {
                                    $answers[] = ['content' => $str, 'order' => $i];
                                    $i++;

                                    return "<u>$str</u>";
                                }
                                $i++;

                                return "$str ";
                            },
                            explode('@@@', chunk_split(implode(' ', $this->faker->words(40)), 10, '@@@'))
                        );

                        $content = implode($paragraphs);

                        $item->setAttribute('content', $content);
                    }

                    if (Str::contains($config['code'] ?? null, 'INTRO')) {
                        $content = match ($config['code']) {
                            'INTRODUCTION_WE' => '<h5><strong>Written Expression</strong></h5><p><strong><u>Directions</u></strong>: In questions 16-40. each sentence has four underlined words or phrases. The four underlined parts of the sentence are marked (A), (B), (C), and (D). Identify the one underlined word or phrase that must be changed in order for the sentence to be correct.</p><p>Look at the following examples.</p><p><strong>Example I</strong>&nbsp;</p><p>(A)<u>The</u>&nbsp;four (B)<u>string</u>&nbsp;on a violin (C)<u>are</u>&nbsp;(D<u>)tuned</u>&nbsp;in fifths.</p><p>&nbsp;The sentence should read, "The four strings on a violin are tuned in fifths." Therefore, you should &nbsp;choose <strong>(B)</strong>.</p><p><strong>Example II</strong>&nbsp;</p><p>The (A)<u>research</u>&nbsp;(B)<u>for the</u>&nbsp;book Roots (C)<u>taking</u>&nbsp;Alex Haley (D)<u>twelve years</u></p><p>The sentence should read, "The research for the book Roots took Alex Haley twelve years." Therefore, you should choose <strong>(C)</strong>.</p><p>Now begin work on the questions.</p>',
                            'INTRODUCTION_STRUCTURE' => '<p><strong>SECTION 2 STRUCTURE AND WRITTEN EXPRESSION</strong></p><p><strong>Time-25 minutes</strong></p><p><strong>(including the reading of the directions)</strong></p><p><strong>&nbsp;</strong></p><p>This section is designed to measure your ability to recognize language that is appropriate for standard written English. There are two types of questions in this section, with special directions for each type.</p><p>&nbsp;</p><h5><strong>Structure</strong></h5><p><strong><u>Directions</u></strong>: Questions 1-15 are incomplete sentences. Beneath each sentence you will see four words or phrases, marked (A), (B), (C), and (D). Choose the one word or phrase that best completes the sentence.</p><p>Look at the following examples.</p><p><strong>Example I</strong></p><p>        The president _____ the election by a landslide.</p><p>        (A) won</p><p>        (B) he won</p><p>        (C) yesterday</p><p>        (D) fortunately</p><p>The sentence should read, "The president won the election by a landslide." Therefore, you should choose <strong>(A)</strong>.</p><p><strong>Example II</strong></p><p>        When _____ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;the conference?</p><p>        (A) the doctor attended</p><p>        (B) did the doctor attend</p><p>        (C) the doctor will attend</p><p>        (D) the doctor\'s attendance</p><p>&nbsp;</p><p>The sentence should read, \'When did the doctor attend the conference?" Therefore, you should choose <strong>(B</strong>). Now begin work on the questions.</p>',
                        };

                        $item->setAttribute('content', $content);
                    }

                    $item->setAttribute('order', $index);
                    $item->fill(Arr::only($config, ['type', 'item_count', 'answer_order_random']));

                    $config['code'] ??= 'GRAMMAR-'.strtoupper(Str::kebab($child->config['title'])).'-'.$index;
                    $item->setAttribute('code', $config['code']);
                    if ($item->save()) {
                        $this->infoCreated($item);

                        if (! Str::contains($config['code'], 'INTRO') && $categories->count() > 0) {
                            /**
                             * @var $category \App\Entities\Classification
                             */
                            $category = $categories->pop();
                            $this->command->warn($item->getAttribute('code').' attached with ['.
                                $category->getAttribute('name').']!');
                            $item->classifications()->attach($category);

                            if (! isset($answers)) {
                                $this->generateAnswers($item);
                            } else {
                                $answers[0]['correct_answer'] = true;
                                $item->answers()->createMany($answers);
                            }
                        }
                    }
                };
            };

            // create introduction
            $child->introductions()->attach(Item::factory()->count(1)->make()->each($makeGenerator([
                'code' => $child->config['intro_code'],
            ]))->first->id, ['type' => Package::PACKAGE_ITEM_TYPE_INTRO]);

            // create items
            $child->items()->syncWithoutDetaching(
                Item::factory()->count(Arr::get($child->config, 'item_total'))->make()->each($makeGenerator()));
        }
    }

    private function createQuestionForListeningSection(Package $package): void
    {
        /**
         * @var $attachment Attachment
         */
        $attachment = Attachment::query()->first();

        foreach ($package->children as $child) {
            /**
             * @var $child Package
             */
            $configItem = $child->config['item'];

            $categories = $this->getCategories($child);

            $makeGenerator = function (array $customConfig = []) use ($configItem, $child, $attachment, &$categories) {
                $config = array_merge($configItem, $customConfig);

                return function (Item $item, int $index) use ($child, $config, $attachment, &$categories) {
                    $item->setAttribute('content', '<p>'.$this->faker->text().'</p>');
                    $item->fill(Arr::only($config, ['type', 'duration', 'answer_order_random', 'item_count']));
                    $item->setAttribute('order', $index);

                    $config['code'] ??= 'LISTENING-'.strtoupper(Str::kebab($child->config['title'])).'-'.$index;
                    $item->setAttribute('code', $config['code']);

                    if (Str::contains($config['code'], 'INTRO')) {
                        $content = match ($config['code']) {
                            'INTRODUCTION_PART_A' => '<p><strong>SECTION&nbsp;1</strong></p><p><strong>LISTENING&nbsp;COMPREHENSION</strong></p><p><strong>Time—approximately&nbsp;35&nbsp;minutes</strong></p><p><strong>(including&nbsp;the&nbsp;reading&nbsp;of&nbsp;the&nbsp;directions&nbsp;for&nbsp;each&nbsp;part)</strong></p><p>&nbsp;</p><p>In&nbsp;this&nbsp;section,&nbsp;you&nbsp;will&nbsp;demonstrate&nbsp;your&nbsp;skills&nbsp;in&nbsp;understanding&nbsp;spoken&nbsp;English.&nbsp;There&nbsp;are&nbsp;three&nbsp;parts&nbsp;in&nbsp;the&nbsp;listening&nbsp;comprehension&nbsp;section&nbsp;with&nbsp;different&nbsp;tasks&nbsp;in&nbsp;each.</p><p></p><h5><strong>Part&nbsp;A</strong></h5><p><strong><u>Directions:</u></strong>&nbsp;In&nbsp;Part&nbsp;A,&nbsp;you&nbsp;will&nbsp;hear&nbsp;short&nbsp;conversations&nbsp;between&nbsp;two&nbsp;people.&nbsp;At&nbsp;the&nbsp;end&nbsp;of&nbsp;each&nbsp;conversation,&nbsp;a&nbsp;third&nbsp;person&nbsp;will&nbsp;ask&nbsp;a&nbsp;question&nbsp;about&nbsp;what&nbsp;the&nbsp;two&nbsp;people&nbsp;say.&nbsp;Each&nbsp;conversation&nbsp;and&nbsp;each&nbsp;question&nbsp;will&nbsp;be&nbsp;spoken&nbsp;only&nbsp;one&nbsp;time.For&nbsp;this&nbsp;reason,&nbsp;you&nbsp;must&nbsp;listen&nbsp;carefully&nbsp;to&nbsp;understand&nbsp;what&nbsp;each&nbsp;speaker&nbsp;says.&nbsp;After&nbsp;you&nbsp;hear&nbsp;a&nbsp;conversation&nbsp;and&nbsp;the&nbsp;question,&nbsp;read&nbsp;the&nbsp;four&nbsp;selections&nbsp;and&nbsp;choose&nbsp;the&nbsp;one&nbsp;that&nbsp;is&nbsp;the&nbsp;best&nbsp;answer&nbsp;to&nbsp;the&nbsp;question&nbsp;the&nbsp;speaker&nbsp;asks.</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Listen&nbsp;to&nbsp;the&nbsp;following&nbsp;example.</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;On&nbsp;the&nbsp;recording,&nbsp;you&nbsp;hear:</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(man)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>Does&nbsp;the&nbsp;car&nbsp;need&nbsp;to&nbsp;be&nbsp;filled?</em></p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(woman)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>Mary&nbsp;stopped&nbsp;to&nbsp;the&nbsp;gas&nbsp;station&nbsp;on&nbsp;her&nbsp;way&nbsp;home.</em></p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(narrator)&nbsp;&nbsp;&nbsp;&nbsp;<em>What&nbsp;does&nbsp;the&nbsp;woman&nbsp;mean?</em>&nbsp;</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;In&nbsp;your&nbsp;test&nbsp;book,&nbsp;you&nbsp;will&nbsp;read:&nbsp;&nbsp;&nbsp;&nbsp;   (A)&nbsp;Mary&nbsp;bought&nbsp;some&nbsp;food</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(B)&nbsp;Mary&nbsp;had&nbsp;car&nbsp;trouble</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(C)&nbsp;Mary&nbsp;went&nbsp;shopping</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(D)&nbsp;Mary&nbsp;bought&nbsp;some&nbsp;gas</p><p></p><p>From&nbsp;the&nbsp;conversation&nbsp;you&nbsp;learn&nbsp;that&nbsp;Mary&nbsp;stopped&nbsp;to&nbsp;the&nbsp;gas&nbsp;station&nbsp;on&nbsp;her&nbsp;way&nbsp;home.&nbsp;The&nbsp;best&nbsp;answer&nbsp;to&nbsp;the&nbsp;question,&nbsp;“Does&nbsp;the&nbsp;car&nbsp;need&nbsp;to&nbsp;be&nbsp;filled”&nbsp;is&nbsp;(<strong>D</strong>),&nbsp;"&nbsp;Mary&nbsp;bought&nbsp;some&nbsp;gas."&nbsp;Therefore,&nbsp;the&nbsp;correct&nbsp;answer&nbsp;is&nbsp;(<strong>D</strong>).</p><p>Now,&nbsp;let&nbsp;us&nbsp;begin&nbsp;part&nbsp;A&nbsp;with&nbsp;question&nbsp;number&nbsp;1.</p>',
                            'INTRODUCTION_PART_B' => '<p><strong><u>Directions:</u></strong>&nbsp;In this part of the test, you will hear longer conversations. After each conversation, you will hear several questions. The conversations and questions will not be repeated. After you hear a question, read the four possible answers in your test book and choose the best answer.</p>',
                            'INTRODUCTION_PART_C' => '<h5><strong>Part&nbsp;C</strong></h5><p><strong><u>Directions:</u></strong>&nbsp;In&nbsp;part&nbsp;C,&nbsp;you&nbsp;will&nbsp;hear&nbsp;short&nbsp;talks.&nbsp;At&nbsp;the&nbsp;end&nbsp;of&nbsp;each,&nbsp;you&nbsp;will&nbsp;be&nbsp;asked&nbsp;several&nbsp;questions.&nbsp;Each&nbsp;talk&nbsp;and&nbsp;each&nbsp;question&nbsp;will&nbsp;be&nbsp;spoken&nbsp;only&nbsp;one&nbsp;time.&nbsp;After&nbsp;you&nbsp;hear&nbsp;a&nbsp;question,&nbsp;read&nbsp;the&nbsp;four&nbsp;selections&nbsp;and&nbsp;choose&nbsp;the&nbsp;one&nbsp;that&nbsp;is&nbsp;the&nbsp;best&nbsp;answer&nbsp;to&nbsp;the&nbsp;question&nbsp;the&nbsp;speaker&nbsp;asks.</p><p></p><p>Listen&nbsp;to&nbsp;this&nbsp;sample&nbsp;talk.</p><p></p><p>You&nbsp;will&nbsp;you&nbsp;hear:</p><p></p><p>&nbsp;&nbsp;&nbsp;&nbsp;(narrator)&nbsp;&nbsp;&nbsp;&nbsp;Before&nbsp;people&nbsp;used&nbsp;automobiles,&nbsp;they&nbsp;walked&nbsp;road&nbsp;bicycles&nbsp;for&nbsp;short&nbsp;distances&nbsp;and&nbsp;took&nbsp;train-street&nbsp;cars&nbsp;or&nbsp;horse-drawn&nbsp;carriages&nbsp;for&nbsp;long&nbsp;distance&nbsp;travel.&nbsp;When&nbsp;automobiles&nbsp;were&nbsp;first&nbsp;produced,&nbsp;only&nbsp;the&nbsp;rich&nbsp;could&nbsp;afford&nbsp;them.&nbsp;Today,&nbsp;almost&nbsp;every&nbsp;household&nbsp;in&nbsp;the&nbsp;United&nbsp;States&nbsp;owns&nbsp;at&nbsp;least&nbsp;one&nbsp;car&nbsp;and&nbsp;90%&nbsp;of&nbsp;American&nbsp;adults&nbsp;have&nbsp;driver’s&nbsp;licenses.&nbsp;According&nbsp;to&nbsp;the&nbsp;yearly&nbsp;valuable&nbsp;output,&nbsp;the&nbsp;US&nbsp;automobile&nbsp;industry&nbsp;exceeds&nbsp;all&nbsp;other&nbsp;manufacturing&nbsp;industries&nbsp;in&nbsp;the&nbsp;country.&nbsp;As&nbsp;a&nbsp;consumer,&nbsp;this&nbsp;industry&nbsp;also&nbsp;supports&nbsp;other&nbsp;major&nbsp;industries,&nbsp;such&nbsp;as&nbsp;steel,&nbsp;glass,&nbsp;and&nbsp;rubber.&nbsp;Furthermore,&nbsp;approximately&nbsp;12&nbsp;million&nbsp;Americans&nbsp;are&nbsp;employed&nbsp;in&nbsp;the&nbsp;auxiliary&nbsp;service&nbsp;industries&nbsp;consisting&nbsp;of&nbsp;repair&nbsp;shops&nbsp;and&nbsp;service&nbsp;stations.&nbsp;&nbsp;</p><p></p><p>Now&nbsp;listen&nbsp;to&nbsp;the&nbsp;following&nbsp;question.&nbsp;You&nbsp;will&nbsp;hear:</p><p></p><p>(narrator)&nbsp;<em>According&nbsp;to&nbsp;the&nbsp;speaker,&nbsp;how&nbsp;do&nbsp;people&nbsp;travel&nbsp;before&nbsp;the&nbsp;invention&nbsp;of&nbsp;the&nbsp;automobile?</em></p><p>You&nbsp;will&nbsp;read:&nbsp;&nbsp;&nbsp;  &nbsp;(A)&nbsp;“By&nbsp;cars&nbsp;and&nbsp;carriages”</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(B)&nbsp;“By&nbsp;bicycles,&nbsp;trains,&nbsp;and&nbsp;carriages”</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(C)&nbsp;“On&nbsp;foot&nbsp;and&nbsp;by&nbsp;boat”</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(D)&nbsp;“On&nbsp;board-ships&nbsp;and&nbsp;trains”</p><p>The&nbsp;best&nbsp;answer&nbsp;to&nbsp;the&nbsp;question,&nbsp;"&nbsp;According&nbsp;to&nbsp;the&nbsp;speaker,&nbsp;how&nbsp;did&nbsp;people&nbsp;travel&nbsp;before&nbsp;the&nbsp;invention&nbsp;of&nbsp;automobile?"&nbsp;is&nbsp;(<strong>B</strong>),&nbsp;"&nbsp;by&nbsp;bicycles,&nbsp;trains,&nbsp;and&nbsp;carriages."&nbsp;Therefore,&nbsp;the&nbsp;correct&nbsp;answer&nbsp;is&nbsp;(<strong>B</strong>).</p><p></p><p>Now&nbsp;listen&nbsp;to&nbsp;another&nbsp;sample&nbsp;question.</p><p></p><p>(narrator)&nbsp;<em>Approximately&nbsp;how&nbsp;many&nbsp;people&nbsp;are&nbsp;employed&nbsp;in&nbsp;automobile&nbsp;service&nbsp;industry?</em>&nbsp;</p><p>You&nbsp;will&nbsp;read:&nbsp;&nbsp;&nbsp;  &nbsp;(A)&nbsp;one&nbsp;million</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(B)&nbsp;ten&nbsp;million</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(C)&nbsp;twelve&nbsp;million</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(D)&nbsp;ninety&nbsp;million</p><p>The&nbsp;best&nbsp;answer&nbsp;to&nbsp;the&nbsp;question,&nbsp;"&nbsp;Approximately&nbsp;how&nbsp;many&nbsp;people&nbsp;are&nbsp;employed&nbsp;in&nbsp;automobile&nbsp;service&nbsp;industry?"&nbsp;is&nbsp;(<strong>C</strong>),&nbsp;"&nbsp;twelve&nbsp;million&nbsp;"&nbsp;Therefore,&nbsp;the&nbsp;correct&nbsp;answer&nbsp;is&nbsp;(<strong>C</strong>).</p>',
                        };

                        $item->setAttribute('content', $content);
                    }

                    if ($item->save()) {
                        $this->infoCreated($item);

                        if (! Str::contains($config['code'], 'INTRO')) {
                            $item->attachments()->attach($attachment->id);

                            if (Arr::get($config, 'item_count', 0) > 0) {
                                Item::factory()->count(Arr::get($config, 'item_count', 0))->make()
                                    ->each(fn (Item $subItem, int $subIndex) => $subItem->fill(array_merge(
                                        ['parent_id' => $item->getAttribute('id')],
                                        Arr::only($config['sub-item'], ['type', 'duration', 'answer_order_random', 'item_count']),
                                        ['order' => $subIndex, 'code' => $config['code'].'-'.$subIndex]
                                    )))
                                    ->each(fn (Item $subItem) => $subItem->save())
                                    ->each(fn (Item $subItem) => $this->infoCreated($subItem))
                                    ->each(fn (Item $subItem) => $this->generateAnswers($subItem))
                                    ->each(fn (Item $subItem) => $subItem->attachments()->attach($attachment->id));
                            } else {
                                $this->generateAnswers($item);
                            }
                        } else {
                            /** @var Attachment $attachment */
                            $attachment = match ($config['code']) {
                                'INTRODUCTION_PART_A' => Attachment::query()->where('title', 'intro_part_a')->firstOrFail(),
                                'INTRODUCTION_PART_B' => Attachment::query()->where('title', 'intro_part_b')->firstOrFail(),
                                'INTRODUCTION_PART_C' => Attachment::query()->where('title', 'intro_part_c')->firstOrFail(),
                            };

                            $item->attachments()->attach($attachment->id);
                        }

                        if (! Str::contains($config['code'], 'INTRO') && $categories->count() > 0) {
                            /**
                             * @var $category \App\Entities\Classification
                             */
                            $category = $categories->pop();
                            $this->command->warn($item->getAttribute('code').' attached with '.
                                $category->getAttribute('name').'!');
                            $item->classifications()->attach($category);
                        }
                    }
                };
            };

            // create introduction
            $child->introductions()->attach(
                Item::factory()->count(1)
                    ->make()->each($makeGenerator(array_merge([
                        'code' => $child->config['intro_code'],
                    ], (empty($configItem['item_count']) ? [] : ['item_count' => 1])))),
                ['type' => Package::PACKAGE_ITEM_TYPE_INTRO]
            );

            // create items
            $child->items()->syncWithoutDetaching(
                Item::factory()->count(Arr::get($child->config, 'item_total'))->make()->each($makeGenerator())
            );
        }
    }
}
