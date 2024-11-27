<?php

namespace Database\Seeders;

use App\Entities\Account\User;
use App\Entities\Media\Attachment;
use Illuminate\Database\Seeder;

class AttachmentsTableSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * @var $user User
         */
        $user = User::query()->firstOrFail();

        $this->command->info('registering dummy audio.');
        Attachment::query()->updateOrCreate([
            'user_id' => $user->id,
            'title' => 'dummy',
            'mime' => 'audio/mpeg',
        ], [
            'user_id' => $user->id,
            'title' => 'dummy',
            'mime' => 'audio/mpeg',
            'path' => 'build/dummy.mp3',
        ]);

        $this->command->info('registering beep audio.');
        Attachment::query()->updateOrCreate([
            'user_id' => $user->id,
            'title' => 'beep',
            'mime' => 'audio/mpeg',
        ], [
            'user_id' => $user->id,
            'title' => 'beep',
            'mime' => 'audio/mpeg',
            'path' => 'build/beep.mp3',
        ]);

        $this->command->info('=> registering audio : introduction');
        collect([
            [
                'user_id' => $user->id,
                'title' => 'intro_part_a',
                'mime' => 'audio/mpeg',
                'path' => 'build/intro_part_a.mp3',
            ],
            [
                'user_id' => $user->id,
                'title' => 'intro_part_b',
                'mime' => 'audio/mpeg',
                'path' => 'build/intro_part_b.mp3',
            ],
            [
                'user_id' => $user->id,
                'title' => 'intro_part_c',
                'mime' => 'audio/mpeg',
                'path' => 'build/intro_part_c.mp3',
            ],
        ])->each(fn(array $info) => Attachment::query()->updateOrCreate($info));

        for ($i = 1; $i <= 50; $i++) {
            $this->command->info('=> registering audio : question-number : ' . $i);
            Attachment::query()->updateOrCreate([
                'user_id' => $user->id,
                'title' => 'etefl_audio_number_'.$i,
                'mime' => 'audio/mpeg',
            ], [
                'user_id' => $user->id,
                'title' => 'etefl_audio_number_'.$i,
                'mime' => 'audio/mpeg',
                'path' => 'build/question-'.$i.'.mp3',
            ]);
        }

        foreach ([31, 35, 39, 43, 47] as $i) {
            $this->command->info('=> registering audio : passage-number : ' . $i);
            Attachment::query()->updateOrCreate([
                'user_id' => $user->id,
                'title' => 'etefl_audio_passage_'.$i.'_to_'.($i + 3),
                'mime' => 'audio/mpeg',
            ], [
                'user_id' => $user->id,
                'title' => 'etefl_audio_passage_'.$i.'_to_'.($i + 3),
                'mime' => 'audio/mpeg',
                'path' => 'build/passage-'.$i.'.mp3',
            ]);
        }

        $this->command->info('=> registering csv : example for import user.');
        Attachment::query()->updateOrCreate([
            'user_id' => $user->id,
            'title' => 'user_example_import',
            'mime' => 'text/csv',
        ], [
            'user_id' => $user->id,
            'title' => 'user_example_import',
            'mime' => 'text/csv',
            'path' => 'build/user_example_import.csv',
        ]);
    }
}
