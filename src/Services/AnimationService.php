<?php

namespace App\Services;

class AnimationService
{
    public function __construct(
        private OctaveService $octaveService
    ) {}

    public function invertedPendulum(array $params): array
    {
        $r = $this->number($params['r'] ?? 0.2, 0.01, 2.0);
        $duration = $this->number($params['duration'] ?? 10, 1, 30);
        $step = $this->number($params['step'] ?? 0.05, 0.01, 0.5);

        $initPosition = $this->number($params['initPosition'] ?? 0, -5, 5);
        $initVelocity = $this->number($params['initVelocity'] ?? 0, -10, 10);
        $initAngle = $this->number($params['initAngle'] ?? 0, -1, 1);
        $initAngularVelocity = $this->number($params['initAngularVelocity'] ?? 0, -10, 10);

        $script = <<<OCTAVE
pkg load control;

M = 0.5;
m = 0.2;
b = 0.1;
I = 0.006;
g = 9.8;
l = 0.3;

p = I*(M+m)+M*m*l^2;

A = [0 1 0 0;
     0 -(I+m*l^2)*b/p (m^2*g*l^2)/p 0;
     0 0 0 1;
     0 -(m*l*b)/p m*g*l*(M+m)/p 0];

B = [0; (I+m*l^2)/p; 0; m*l/p];

C = [1 0 0 0;
     0 0 1 0];

D = [0; 0];

K = lqr(A, B, C'*C, 1);
Ac = A - B*K;
N = -inv(C(1,:) * inv(A - B*K) * B);

sys = ss(Ac, B*N, C, D);

t = 0:$step:$duration;
r = $r;

[y, t, x] = lsim(sys, r * ones(size(t)), t, [$initPosition; $initVelocity; $initAngle; $initAngularVelocity]);

result = struct();
result.time = t(:)';
result.position = y(:,1)';
result.angle = y(:,2)';
result.state = x;
result.finalState = x(size(x,1), :);
result.target = r;

disp(jsonencode(result));
OCTAVE;

        return $this->octaveService->executeScript($script);
    }

    public function ballBeam(array $params): array
    {
        $r = $this->number($params['r'] ?? 0.25, 0.01, 2.0);
        $duration = $this->number($params['duration'] ?? 5, 1, 30);
        $step = $this->number($params['step'] ?? 0.01, 0.005, 0.5);

        $initPosition = $this->number($params['initPosition'] ?? 0, -5, 5);
        $initVelocity = $this->number($params['initVelocity'] ?? 0, -10, 10);
        $initAngle = $this->number($params['initAngle'] ?? 0, -1, 1);
        $initAngularVelocity = $this->number($params['initAngularVelocity'] ?? 0, -10, 10);

        $script = <<<OCTAVE
pkg load control;

m = 0.111;
R = 0.015;
g = -9.8;
J = 9.99e-6;

H = -m*g/(J/(R^2)+m);

A = [0 1 0 0;
     0 0 H 0;
     0 0 0 1;
     0 0 0 0];

B = [0; 0; 0; 1];
C = [1 0 0 0];
D = [0];

K = place(A, B, [-2+2i, -2-2i, -20, -80]);
N = -inv(C * inv(A - B*K) * B);

sys = ss(A - B*K, B, C, D);

t = 0:$step:$duration;
r = $r;

[y, t, x] = lsim(N * sys, r * ones(size(t)), t, [$initPosition; $initVelocity; $initAngle; $initAngularVelocity]);

result = struct();
result.time = t(:)';
result.position = y(:)';
result.angle = x(:,3)';
result.state = x;
result.finalState = x(size(x,1), :);
result.target = r;

disp(jsonencode(result));
OCTAVE;

        return $this->octaveService->executeScript($script);
    }

    private function number(mixed $value, float $min, float $max): float
    {
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException('Invalid numeric parameter.');
        }

        $number = (float) $value;

        if ($number < $min || $number > $max) {
            throw new \InvalidArgumentException("Parameter out of allowed range: $min - $max.");
        }

        return $number;
    }
}