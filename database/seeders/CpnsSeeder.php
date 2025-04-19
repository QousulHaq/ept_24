<?php

namespace Database\Seeders;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Extra\Presets\Cpns;
use Illuminate\Database\Seeder;
use App\Entities\Media\Attachment;
use App\Entities\Question\Package;
use Illuminate\Support\Collection;
use App\Entities\Question\Package\Item;

class CpnsSeeder extends Seeder
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
            ->where('config', Cpns::NAME)
            ->firstOrFail();

        force_queue_sync(function () use ($package) {
            foreach ($package->children as $child) {
                /**
                 * @var $child Package
                 */
                switch ($child->getRawOriginal('config')) {
                    case 'CPNS.twk':
                        $this->command->alert('!!! Start seeder for reading section');
                        $this->createQuestionForReadingSection($child);
                        break;
                    case 'CPNS.tiu':
                        $this->command->alert('!!! Start seeder for reading section');
                        $this->createQuestionForReadingSection($child);
                        break;
                    case 'CPNS.tkp':
                        $this->command->alert('!!! Start seeder for reading section');
                        $this->createQuestionForReadingSection($child);
                        break;
                }
            }

            dispatch(new Cpns\Jobs\Package\BuildNoteForPackage($package));
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
}
