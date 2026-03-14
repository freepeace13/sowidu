<?php

use App\Broadcasting\CompanyPrivateChannel;
use App\Broadcasting\EmployeePrivateChannel;
use App\Broadcasting\OrderChannel;
use App\Broadcasting\TaskChannel;
use App\Broadcasting\TodoBoardChannel;
use App\Broadcasting\UserPrivateChannel;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use App\Support\Facades\Impersonate;

Broadcast::channel('users.{userId}', UserPrivateChannel::class);
Broadcast::channel('employees.{employeeId}', EmployeePrivateChannel::class);
Broadcast::channel('companies.{companyId}', CompanyPrivateChannel::class);

Broadcast::channel('tasks.{taskId}', TaskChannel::class);
Broadcast::channel('boards.{boardId}', TodoBoardChannel::class);

Broadcast::channel('App.Models.User.{user}', function ($auth, User $user) {
    return $user->is(Impersonate::user());
}, ['guards' => ['web']]);

Broadcast::channel('App.Models.Employee.{employee}', function ($user, Employee $employee) {
    return $employee->is(Impersonate::impersonator());
}, ['guards' => ['web']]);

Broadcast::channel('video.transcode.{id}', function ($user) {
    return true;
}, ['guards' => ['web']]);

Broadcast::channel('chat.conversation.{user}', function ($user) {
    return $user->is(Impersonate::user());
}, ['guards' => ['web']]);

Broadcast::channel('orders.{order}', OrderChannel::class, ['guards' => ['web']]);

Broadcast::channel('company.{company}.orders', function ($user, Company $company) {
    return $company->isEmployed($user);
}, ['guards' => ['web']]);
