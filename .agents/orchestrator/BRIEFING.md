# BRIEFING — 2026-07-04T14:08:33+01:00

## Mission
Review the PHP application, extract manuscript requirements, run tests, identify discrepancies, and generate a review report.

## 🔒 My Identity
- Archetype: teamwork_orchestrator
- Roles: orchestrator, user_liaison, human_reporter, successor
- Working directory: c:\Users\Mark Jed M Cagatin\Desktop\sffo\.agents\orchestrator
- Original parent: parent
- Original parent conversation ID: 2fc15928-e3ad-4033-8fca-dedb1592190f

## 🔒 My Workflow
- **Pattern**: Project
- **Scope document**: c:\Users\Mark Jed M Cagatin\Desktop\sffo\PROJECT.md
1. **Decompose**: Decompose the task into analysis, testing, manuscript extraction, and synthesis.
2. **Dispatch & Execute** (pick ONE):
   - **Delegate (sub-orchestrator)**: Delegate milestones to subagents/sub-orchestrators.
3. **On failure** (in this order):
   - Retry: nudge stuck agent or re-send task
   - Replace: spawn fresh agent with partial progress
   - Skip: proceed without (only if non-critical)
   - Redistribute: split stuck agent's remaining work
   - Redesign: re-partition decomposition
   - Escalate: report to parent (sub-orchestrators only, last resort)
4. **Succession**: At 16 spawns, write handoff.md, spawn successor.
- **Work items**:
  1. Initialize configuration and setup files [done]
  2. Extract manuscript requirements [pending]
  3. Run automated tests and report outcomes [pending]
  4. Compare manuscript requirements with actual codebase/behavior [pending]
  5. Generate review_report.md [pending]
- **Current phase**: 1
- **Current focus**: Initialize plan, progress, context and project layout.

## 🔒 Key Constraints
- Never write, modify, or create source code files directly.
- Never run build/test commands yourself — require workers to do so.
- Never reuse a subagent after it has delivered its handoff — always spawn fresh.
- Do not attempt to fix the bugs.

## Current Parent
- Conversation ID: 2fc15928-e3ad-4033-8fca-dedb1592190f
- Updated: not yet

## Key Decisions Made
- Initialized briefing and plan.

## Team Roster
| Agent | Type | Work Item | Status | Conv ID |
|-------|------|-----------|--------|---------|
| explorer_manuscript | teamwork_preview_explorer | Extract and analyze Manuscript.docx | in-progress | 8e71a721-fcff-4fa6-93fb-943f97de207c |
| worker_test_runner | teamwork_preview_worker | Run PHP test suites | in-progress | 38046ff4-17dc-415b-a954-d1f8c711336f |

## Succession Status
- Succession required: no
- Spawn count: 2 / 16
- Pending subagents: 8e71a721-fcff-4fa6-93fb-943f97de207c, 38046ff4-17dc-415b-a954-d1f8c711336f
- Predecessor: none
- Successor: not yet spawned

## Active Timers
- Heartbeat cron: task-23
- Safety timer: none
- On succession: kill all timers before spawning successor
- On context truncation: run manage_task(Action="list") — re-create if missing

## Artifact Index
- c:\Users\Mark Jed M Cagatin\Desktop\sffo\.agents\orchestrator\BRIEFING.md — Coordination briefing
- c:\Users\Mark Jed M Cagatin\Desktop\sffo\.agents\orchestrator\plan.md — Project milestone plan
- c:\Users\Mark Jed M Cagatin\Desktop\sffo\.agents\orchestrator\progress.md — Progress tracking heartbeat
- c:\Users\Mark Jed M Cagatin\Desktop\sffo\.agents\orchestrator\context.md — Context documentation
