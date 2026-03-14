<?php

namespace Modules\Chatly\Tests\Unit\Actions;

use App\Models\User;
use Mockery;
use Modules\Chatly\Actions\AddParticipant;
use Modules\Chatly\Contracts\External\AuthorizationContract;
use Modules\Chatly\Contracts\External\UrnResolverContract;
use Musonza\Chat\Models\Conversation;
use Musonza\Chat\Models\Participation;
use Tests\TestCase;

class AddParticipantTest extends TestCase
{
    protected $authorization;

    protected $urnResolver;

    protected $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorization = Mockery::mock(AuthorizationContract::class);
        $this->urnResolver = Mockery::mock(UrnResolverContract::class);
        $this->action = new AddParticipant($this->authorization, $this->urnResolver);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_authorizes_before_adding_participant()
    {
        $user = Mockery::mock(User::class)->makePartial();
        $joiner = Mockery::mock(User::class)->makePartial();
        $conversation = Mockery::mock(Conversation::class);
        $participation = Mockery::mock(Participation::class);

        $this->authorization->shouldReceive('authorize')
            ->once()
            ->with($user, 'addParticipants', $conversation);

        $conversation->shouldReceive('participantFromSender')
            ->once()
            ->with($joiner)
            ->andReturn($participation);

        $result = $this->action->add($user, $joiner, $conversation);

        $this->assertSame($participation, $result);
    }

    /** @test */
    public function it_resolves_urn_string_to_entity()
    {
        $user = Mockery::mock(User::class)->makePartial();
        $joiner = Mockery::mock(User::class)->makePartial();
        $conversation = Mockery::mock(Conversation::class);
        $participation = Mockery::mock(Participation::class);
        $urn = 'urn:user:42';

        $this->authorization->shouldReceive('authorize')
            ->once()
            ->with($user, 'addParticipants', $conversation);

        $this->urnResolver->shouldReceive('resolve')
            ->once()
            ->with($urn)
            ->andReturn($joiner);

        $conversation->shouldReceive('participantFromSender')
            ->once()
            ->with($joiner)
            ->andReturn($participation);

        $result = $this->action->add($user, $urn, $conversation);

        $this->assertSame($participation, $result);
    }

    /** @test */
    public function it_adds_new_participant_to_conversation()
    {
        $user = Mockery::mock(User::class)->makePartial();
        $joiner = Mockery::mock(User::class)->makePartial();
        $conversation = Mockery::mock(Conversation::class);
        $participation = Mockery::mock(Participation::class);

        $this->authorization->shouldReceive('authorize')
            ->once();

        $conversation->shouldReceive('participantFromSender')
            ->once()
            ->with($joiner)
            ->andReturn(null);

        $conversation->shouldReceive('addParticipants')
            ->once()
            ->with([$joiner])
            ->andReturnSelf();

        $conversation->shouldReceive('participantFromSender')
            ->once()
            ->with($joiner)
            ->andReturn($participation);

        $result = $this->action->add($user, $joiner, $conversation);

        $this->assertSame($participation, $result);
    }

    /** @test */
    public function it_returns_existing_participation_if_already_participant()
    {
        $user = Mockery::mock(User::class)->makePartial();
        $joiner = Mockery::mock(User::class)->makePartial();
        $conversation = Mockery::mock(Conversation::class);
        $participation = Mockery::mock(Participation::class);

        $this->authorization->shouldReceive('authorize')
            ->once();

        $conversation->shouldReceive('participantFromSender')
            ->once()
            ->with($joiner)
            ->andReturn($participation);

        $result = $this->action->add($user, $joiner, $conversation);

        $this->assertSame($participation, $result);
    }
}
